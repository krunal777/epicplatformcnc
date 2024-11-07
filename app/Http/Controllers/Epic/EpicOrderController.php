<?php

namespace App\Http\Controllers\Epic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Epic\EpicOrder;
use MongoDB\BSON\UTCDateTime;
use Carbon\Carbon;

class EpicOrderController extends Controller
{
    public function __construct()
    {

    }


    public function index(Request $request)
    {
        $start_dt = $request->input('start_dt');
        $end_dt = $request->input('end_dt');
        $type = $request->input('type');

        $query = EpicOrder::query();
        if ($start_dt && $end_dt) {
            $query->where('created_at', '>=', new UTCDateTime(strtotime($start_dt) * 1000));
            $query->where('created_at', '<=', new UTCDateTime(strtotime($end_dt) * 1000));
        }

        $orders = $query->get();

        // Current period calculations
        $currentRevenue = $this->calculateNetRevenue($orders);
        $currentRevenueWithTax = $orders->sum(fn($order) => $order->summary['total_due']['amount']);
        $currentProductsSold = $orders->sum(fn($order) => $order->summary['total_items_count']);
        $currentOrderTotal = count($orders);

        // Previous period calculations
        $previousOrders = $this->getPreviousPeriodOrders($type);
        $previousRevenue = $this->calculateNetRevenue($previousOrders);
        $previousRevenueWithTax = $previousOrders->sum(fn($order) => $order->summary['total_due']['amount']);
        $previousProductsSold = $previousOrders->sum(fn($order) => $order->summary['total_items_count']);
        $previousOrderTotal = count($previousOrders);

        // Percentage change calculations
        $revenueChange = $this->calculatePercentageChange($currentRevenue, $previousRevenue);
        $revenueWithTaxChange = $this->calculatePercentageChange($currentRevenueWithTax, $previousRevenueWithTax);
        $productsSoldChange = $this->calculatePercentageChange($currentProductsSold, $previousProductsSold);
        $orderTotalChange = $this->calculatePercentageChange($currentOrderTotal, $previousOrderTotal);

        $data = [
            'gross_revenue_total' => $currentRevenue,
            'revenue_with_tax' => $currentRevenueWithTax,
            'order_total' => $currentOrderTotal,
            'products_sold' => $currentProductsSold,
            'percentage_changes' => [
                'gross_revenue_total' => $revenueChange,
                'revenue_with_tax' => $revenueWithTaxChange,
                'order_total' => $orderTotalChange,
                'products_sold' => $productsSoldChange,
            ]
        ];

        return response(['data' => $data]);
    }

    private function getPreviousPeriodOrders($type)
    {
        $query = EpicOrder::query();
        switch ($type) {
            case 'today':
                $start_dt = Carbon::now()->subDay()->startOfDay();
                $end_dt = Carbon::now()->startOfDay();
                break;
            case 'this_week':
                $start_dt = Carbon::now()->subWeek()->startOfWeek();
                $end_dt = Carbon::now()->startOfWeek();
                break;
            case 'this_month':
                $start_dt = Carbon::now()->subMonth()->startOfMonth();
                $end_dt = Carbon::now()->startOfMonth();
                break;
            case 'this_year':
                $start_dt = Carbon::now()->subYear()->startOfYear();
                $end_dt = Carbon::now()->startOfYear();
                break;
            default:
                return collect();
        }
        $start = new UTCDateTime($start_dt->timestamp * 1000);
        $end = new UTCDateTime($end_dt->timestamp * 1000);
        $query->where('created_at', '>=', $start);
        $query->where('created_at', '<=', $end);

        return $query->get();
    }

    private function calculateNetRevenue($orders)
    {
        return $orders->sum(function ($order) {
            $totalItemsAmount = $order->summary['total_items_amount']['amount'];
            $totalShippingAmount = $order->summary['total_shipping']['amount'];
            $totalDiscountAmount = $order->summary['total_discount']['amount'];
            return $totalItemsAmount + $totalShippingAmount - $totalDiscountAmount;
        });
    }

    private function calculatePercentageChange($current, $previous)
    {
        if ($previous == 0) {
            return $current == 0 ? 0 : 100;
        }
        return (($current - $previous) / $previous) * 100;
    }
}



