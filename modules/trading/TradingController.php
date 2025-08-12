<?php
/**
 * Trading Controller - Demo Trading Interface
 */

require_once CORE_PATH . '/Controller.php';

class TradingController extends Controller
{
    public function index()
    {
        $this->view('dashboard', [
            'title' => 'Trading Dashboard',
            'custom_css' => ['/modules/trading/assets/trading.css'],
            'custom_js' => ['/modules/trading/assets/trading.js']
        ]);
    }

    public function symbol($symbol = null)
    {
        if (!$symbol) {
            header('Location: /trading');
            exit;
        }

        // Get symbol information
        $symbolData = $this->db->selectOne("
            SELECT s.*, p.price, p.change_percent_24h, p.volume_24h
            FROM symbols s
            LEFT JOIN prices p ON s.id = p.symbol_id
            WHERE s.symbol = ? AND s.status = 'active'
        ", [$symbol]);

        if (!$symbolData) {
            $this->setFlashMessage('Sembol bulunamadı.', 'error');
            header('Location: /trading');
            exit;
        }

        $this->view('symbol', [
            'title' => "Trading - {$symbolData['symbol']}",
            'symbol' => $symbolData,
            'custom_css' => ['/modules/trading/assets/trading.css'],
            'custom_js' => ['/modules/trading/assets/trading.js']
        ]);
    }

    public function placeOrder()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $symbol = $_POST['symbol'] ?? '';
        $side = $_POST['side'] ?? '';
        $type = $_POST['type'] ?? 'market';
        $quantity = floatval($_POST['quantity'] ?? 0);
        $price = floatval($_POST['price'] ?? 0);

        // Basic validation
        if (empty($symbol) || empty($side) || $quantity <= 0) {
            echo json_encode(['success' => false, 'message' => 'Geçersiz emir parametreleri']);
            exit;
        }

        if (!in_array($side, ['buy', 'sell'])) {
            echo json_encode(['success' => false, 'message' => 'Geçersiz emir yönü']);
            exit;
        }

        if (!in_array($type, ['market', 'limit'])) {
            echo json_encode(['success' => false, 'message' => 'Geçersiz emir türü']);
            exit;
        }

        // For demo purposes, simulate order placement
        $orderId = 'ORD_' . time() . '_' . rand(1000, 9999);
        
        // Get current price for market orders
        if ($type === 'market') {
            $symbolData = $this->db->selectOne("
                SELECT p.price FROM symbols s
                JOIN prices p ON s.id = p.symbol_id
                WHERE s.symbol = ?
            ", [$symbol]);
            
            $price = $symbolData['price'] ?? 0;
        }

        $response = [
            'success' => true,
            'message' => 'Emir başarıyla oluşturuldu',
            'order' => [
                'id' => $orderId,
                'symbol' => $symbol,
                'side' => $side,
                'type' => $type,
                'quantity' => $quantity,
                'price' => $price,
                'status' => $type === 'market' ? 'filled' : 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];

        echo json_encode($response);
        exit;
    }

    public function getOrders()
    {
        header('Content-Type: application/json');
        
        // For demo purposes, return mock orders
        $orders = [
            [
                'id' => 'ORD_001',
                'symbol' => 'BTCUSDT',
                'side' => 'buy',
                'type' => 'limit',
                'quantity' => 0.001,
                'price' => 44500.00,
                'filled' => 0,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour'))
            ],
            [
                'id' => 'ORD_002',
                'symbol' => 'ETHUSDT',
                'side' => 'sell',
                'type' => 'market',
                'quantity' => 0.1,
                'price' => 3200.00,
                'filled' => 0.1,
                'status' => 'filled',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
            ]
        ];

        echo json_encode(['success' => true, 'orders' => $orders]);
        exit;
    }

    public function cancelOrder($orderId = null)
    {
        header('Content-Type: application/json');
        
        if (!$orderId) {
            echo json_encode(['success' => false, 'message' => 'Emir ID gerekli']);
            exit;
        }

        // For demo purposes, always return success
        echo json_encode([
            'success' => true,
            'message' => 'Emir başarıyla iptal edildi',
            'order_id' => $orderId
        ]);
        exit;
    }

    public function portfolio()
    {
        $this->view('portfolio', [
            'title' => 'Portfolio',
            'custom_css' => ['/modules/trading/assets/trading.css'],
            'custom_js' => ['/modules/trading/assets/trading.js']
        ]);
    }

    public function orders()
    {
        $this->view('orders', [
            'title' => 'Emirlerim',
            'custom_css' => ['/modules/trading/assets/trading.css'],
            'custom_js' => ['/modules/trading/assets/trading.js']
        ]);
    }
}
