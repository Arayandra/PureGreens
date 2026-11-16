<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class AprioriRecommendationService
{
    public function getRecommendations($productId, $userId = null)
    {
        try {
            $pythonScript = base_path('python/apriori_recommendation.py');
            $dataFile = storage_path('app/transactions.csv');

            // Do not run the algorithm with a stale CSV export when no transactions exist.
            if (!$this->exportTransactionsToCsv($dataFile, $userId)) {
                return [];
            }

            // Jalankan skrip Python. PYTHON_BINARY dapat disesuaikan untuk Windows/Linux.
            $pythonBinary = config('recommendations.python_binary', 'python3');
            $command = escapeshellarg($pythonBinary)
                . ' ' . escapeshellarg($pythonScript)
                . ' ' . escapeshellarg($dataFile)
                . ' ' . escapeshellarg((string) $productId);
            $output = shell_exec($command);

            if ($output === null) {
                Log::warning('Python recommendation process did not return output.');
                return [];
            }

            $recommendations = json_decode(trim($output), true);

            if (!is_array($recommendations) || array_key_exists('error', $recommendations)) {
                Log::warning('Apriori recommendation script returned an invalid or error response.');
                return [];
            }

            $productId = (int) $productId;
            $validated = [];

            foreach ($recommendations as $candidate) {
                $recommendationId = filter_var($candidate, FILTER_VALIDATE_INT);

                if ($recommendationId === false || $recommendationId < 1 || $recommendationId === $productId) {
                    continue;
                }

                $validated[] = $recommendationId;
            }

            return array_values(array_unique($validated));
        } catch (\Throwable $e) {
            Log::error("Apriori Recommendation Error: " . $e->getMessage());
            return [];
        }
    }

    private function exportTransactionsToCsv($filePath, $userId = null)
{
    try {
        $query = \App\Models\OrderItem::select('order_id', 'product_id')
            ->whereHas('order', function ($query) use ($userId) {
                $query->where('created_at', '>=', now()->subMonths(3));
                if ($userId) {
                    $query->where('user_id', $userId);
                }
            });

        $transactions = $query->get()
            ->groupBy('order_id')
            ->map(function ($group) {
                return $group->pluck('product_id')->toArray();
            })
            ->values()
            ->toArray();

        if (empty($transactions) && $userId) {
            Log::warning("No transactions for user {$userId}, using all transactions.");
            $query = \App\Models\OrderItem::select('order_id', 'product_id')
                ->whereHas('order', function ($query) {
                    $query->where('created_at', '>=', now()->subMonths(3));
                });
            $transactions = $query->get()
                ->groupBy('order_id')
                ->map(function ($group) {
                    return $group->pluck('product_id')->toArray();
                })
                ->values()
                ->toArray();
        }

        $transactionCount = count($transactions);
        $itemCount = array_sum(array_map('count', $transactions));
        Log::info('Prepared transaction data for product recommendations.', [
            'transaction_count' => $transactionCount,
            'item_count' => $itemCount,
            'customer_scoped' => $userId !== null,
        ]);
        if (empty($transactions)) {
            Log::warning("No transactions found.");
            return false;
        }

        $directory = dirname($filePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $file = fopen($filePath, 'w');
        if ($file === false) {
            Log::error("Failed to open CSV file for writing at {$filePath}");
            return false;
        }

        fputcsv($file, ['order_id', 'product_id']);
        foreach ($transactions as $orderId => $products) {
            foreach ($products as $productId) {
                fputcsv($file, [$orderId, $productId]);
            }
        }
        fclose($file);

        Log::info("CSV file created at {$filePath}. Size: " . filesize($filePath) . " bytes");
        return true;

    } catch (\Throwable $e) {
        Log::error("Error exporting transactions for user {$userId}: " . $e->getMessage());
        return false;
    }
}
}