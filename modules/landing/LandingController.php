<?php
/**
 * Landing Page Controller
 */

require_once CORE_PATH . '/Controller.php';

class LandingController extends Controller
{
    private $symbolModel;
    private $priceModel;
    private $blogModel;

    public function __construct()
    {
        parent::__construct();
        $this->symbolModel = new SymbolModel();
        $this->priceModel = new PriceModel();
        $this->blogModel = new BlogModel();
    }

    public function index()
    {
        try {
            // Get featured symbols with current prices
            $featuredSymbols = $this->getFeaturedSymbols();
            
            // Get top movers (biggest gainers and losers)
            $topMovers = $this->getTopMovers();
            
            // Get latest blog posts
            $latestPosts = $this->getLatestBlogPosts();
            
            // Get market statistics
            $marketStats = $this->getMarketStatistics();

            $this->view('index', [
                'title' => 'Ana Sayfa',
                'featured_symbols' => $featuredSymbols,
                'top_movers' => $topMovers,
                'latest_posts' => $latestPosts,
                'market_stats' => $marketStats,
                'custom_css' => ['/modules/landing/assets/landing.css'],
                'custom_js' => ['/modules/landing/assets/landing.js']
            ]);
        } catch (Exception $e) {
            if (DEBUG_MODE) {
                throw $e;
            } else {
                $this->view('error', [
                    'title' => 'Hata',
                    'message' => 'Sayfa yüklenirken bir hata oluştu.'
                ]);
            }
        }
    }

    private function getFeaturedSymbols()
    {
        try {
            $sql = "
                SELECT s.*, p.price, p.change_percent_24h, p.volume_24h
                FROM symbols s
                LEFT JOIN prices p ON s.id = p.symbol_id
                WHERE s.is_featured = 1 AND s.status = 'active'
                ORDER BY s.symbol
                LIMIT 8
            ";
            
            return $this->db->select($sql);
        } catch (Exception $e) {
            // Fallback: return symbols without price data
            $sql = "
                SELECT s.*, 0 as price, 0 as change_percent_24h, 0 as volume_24h
                FROM symbols s
                WHERE s.is_featured = 1 AND s.status = 'active'
                ORDER BY s.symbol
                LIMIT 8
            ";
            
            return $this->db->select($sql);
        }
    }

    private function getTopMovers()
    {
        try {
            // Get biggest gainers
            $gainers = $this->db->select("
                SELECT s.symbol, s.name, p.price, p.change_percent_24h
                FROM symbols s
                JOIN prices p ON s.id = p.symbol_id
                WHERE s.status = 'active' AND p.change_percent_24h > 0
                ORDER BY p.change_percent_24h DESC
                LIMIT 5
            ");

            // Get biggest losers
            $losers = $this->db->select("
                SELECT s.symbol, s.name, p.price, p.change_percent_24h
                FROM symbols s
                JOIN prices p ON s.id = p.symbol_id
                WHERE s.status = 'active' AND p.change_percent_24h < 0
                ORDER BY p.change_percent_24h ASC
                LIMIT 5
            ");

            return [
                'gainers' => $gainers,
                'losers' => $losers
            ];
        } catch (Exception $e) {
            // Return mock data if there's an error
            return [
                'gainers' => [
                    ['symbol' => 'BTCUSDT', 'name' => 'Bitcoin / Tether', 'price' => 45000, 'change_percent_24h' => 2.5],
                    ['symbol' => 'ETHUSDT', 'name' => 'Ethereum / Tether', 'price' => 3200, 'change_percent_24h' => 1.8]
                ],
                'losers' => [
                    ['symbol' => 'EURUSD', 'name' => 'Euro / US Dollar', 'price' => 1.0850, 'change_percent_24h' => -0.3],
                    ['symbol' => 'GBPUSD', 'name' => 'British Pound / US Dollar', 'price' => 1.2650, 'change_percent_24h' => -0.5]
                ]
            ];
        }
    }

    private function getLatestBlogPosts()
    {
        try {
            $sql = "
                SELECT bp.*, u.username as author_name
                FROM blog_posts bp
                JOIN users u ON bp.author_id = u.id
                WHERE bp.status = 'published' AND bp.published_at <= NOW()
                ORDER BY bp.published_at DESC
                LIMIT 6
            ";
            
            return $this->db->select($sql);
        } catch (Exception $e) {
            // Return empty array if blog_posts table doesn't exist
            return [];
        }
    }

    private function getMarketStatistics()
    {
        try {
            // Total symbols
            $totalSymbols = $this->db->selectOne("
                SELECT COUNT(*) as count FROM symbols WHERE status = 'active'
            ")['count'] ?? 0;

            // Total 24h volume
            $totalVolume = $this->db->selectOne("
                SELECT SUM(volume_24h) as total FROM prices
            ")['total'] ?? 0;

            // Active users (use created_at since last_login might not exist)
            $activeUsers = $this->db->selectOne("
                SELECT COUNT(*) as count FROM users 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
            ")['count'] ?? 0;

            // Market cap (mock calculation)
            $marketCap = $this->db->selectOne("
                SELECT SUM(price * volume_24h) as cap FROM prices
            ")['cap'] ?? 0;

            return [
                'total_symbols' => $totalSymbols,
                'total_volume' => $totalVolume,
                'active_users' => $activeUsers,
                'market_cap' => $marketCap
            ];
        } catch (Exception $e) {
            // Return default values if there's an error
            return [
                'total_symbols' => 4,
                'total_volume' => 125000,
                'active_users' => 1,
                'market_cap' => 500000
            ];
        }
    }
}

// Symbol Model
class SymbolModel extends Model
{
    protected $table = 'symbols';
    protected $fillable = [
        'symbol', 'name', 'base_currency', 'quote_currency', 'type',
        'min_trade_amount', 'max_trade_amount', 'price_precision',
        'quantity_precision', 'trading_fee', 'is_featured'
    ];

    public function getFeatured()
    {
        return $this->where('is_featured = 1 AND status = ?', ['active']);
    }

    public function getByType($type)
    {
        return $this->where('type = ? AND status = ?', [$type, 'active']);
    }

    public function search($query, $fields = ['symbol', 'name'], $page = 1, $perPage = null)
    {
        // Use the parent search method with proper fields
        return parent::search($query, $fields, $page, $perPage);
    }
    
    public function searchSymbols($query)
    {
        return $this->where(
            '(symbol LIKE ? OR name LIKE ?) AND status = ?',
            ["%$query%", "%$query%", 'active']
        );
    }
}

// Price Model
class PriceModel extends Model
{
    protected $table = 'prices';
    protected $fillable = [
        'symbol_id', 'price', 'bid', 'ask', 'volume_24h',
        'change_24h', 'change_percent_24h', 'high_24h', 'low_24h'
    ];

    public function getBySymbol($symbolId)
    {
        return $this->findBy('symbol_id', $symbolId);
    }

    public function updatePrice($symbolId, $priceData)
    {
        $existing = $this->getBySymbol($symbolId);
        
        if ($existing) {
            return $this->update($existing['id'], $priceData);
        } else {
            $priceData['symbol_id'] = $symbolId;
            return $this->create($priceData);
        }
    }

    public function getTopMovers($limit = 10)
    {
        $sql = "
            SELECT p.*, s.symbol, s.name
            FROM prices p
            JOIN symbols s ON p.symbol_id = s.id
            WHERE s.status = 'active'
            ORDER BY ABS(p.change_percent_24h) DESC
            LIMIT ?
        ";
        
        return $this->db->select($sql, [$limit]);
    }
}

// Blog Model
class BlogModel extends Model
{
    protected $table = 'blog_posts';
    protected $fillable = [
        'title', 'slug', 'content', 'excerpt', 'featured_image',
        'author_id', 'category', 'status', 'is_featured', 'published_at'
    ];

    public function getPublished($limit = null)
    {
        $sql = "
            SELECT * FROM {$this->table}
            WHERE status = 'published' AND published_at <= NOW()
            ORDER BY published_at DESC
        ";
        
        if ($limit) {
            $sql .= " LIMIT $limit";
        }
        
        return $this->db->select($sql);
    }

    public function getFeatured($limit = 3)
    {
        $sql = "
            SELECT * FROM {$this->table}
            WHERE status = 'published' AND is_featured = 1 AND published_at <= NOW()
            ORDER BY published_at DESC
            LIMIT ?
        ";
        
        return $this->db->select($sql, [$limit]);
    }

    public function getByCategory($category, $limit = null)
    {
        $conditions = "category = ? AND status = 'published' AND published_at <= NOW()";
        $params = [$category];
        
        return $this->where($conditions, $params, 'published_at DESC', $limit);
    }

    public function generateSlug($title)
    {
        // Convert Turkish characters
        $turkish = ['ç', 'ğ', 'ı', 'ö', 'ş', 'ü', 'Ç', 'Ğ', 'I', 'İ', 'Ö', 'Ş', 'Ü'];
        $english = ['c', 'g', 'i', 'o', 's', 'u', 'c', 'g', 'i', 'i', 'o', 's', 'u'];
        $title = str_replace($turkish, $english, $title);
        
        // Generate slug
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        
        // Check if slug exists and make it unique
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->exists('slug = ?', [$slug])) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
}
