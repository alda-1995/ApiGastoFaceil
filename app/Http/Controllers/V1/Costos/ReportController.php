<?php

namespace App\Http\Controllers\V1\Costos;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportController extends Controller
{

    public function getTotalsTransactions($userId)
    {
        try {
            $dateCurrent = Carbon::now()->toDateString();
            $yearCurrent = Carbon::now()->year;

            $queryByCurrent = Transaction::where("user_id", $userId)
                ->where('currentDate', $dateCurrent)
                ->get();
            $totalAmountDay = number_format((float)$queryByCurrent->sum('amount'), 2);

            $monthCurrent = Carbon::now()->month;
            $startDateMonth = Carbon::create($yearCurrent, $monthCurrent, 1, 0, 0, 0, 'UTC');
            $endDateMonth = $startDateMonth->copy()->endOfMonth();

            $queryByMonth = Transaction::where("user_id", $userId)
                ->whereBetween('currentDate', [$startDateMonth, $endDateMonth])
                ->get();

            $totalAmountMonth = number_format((float)$queryByMonth->sum('amount'), 2);

            $startDateYear = Carbon::createFromDate($yearCurrent, 1, 1, 'UTC')->startOfDay();
            $endDateYear = Carbon::createFromDate($yearCurrent, 12, 31, 'UTC')->endOfDay();


            $queryDateYear = Transaction::where("user_id", $userId)
                ->whereBetween('currentDate', [$startDateYear, $endDateYear])
                ->get();

            $totalAmountYear = number_format((float)$queryDateYear->sum('amount'), 2);

            return response()->json([
                'totalDay' => $totalAmountDay,
                'totalMonth' => $totalAmountMonth,
                'totalYear' => $totalAmountYear
            ], Response::HTTP_OK);
        } catch (Exception $ex) {
            return response()->json(['error' => "Error al consultar tus gastos"], 500);
        }
    }

    public function getTransactionsMonth($month, $userId)
    {
        try {
            $year = Carbon::now()->year;
            $startDate = Carbon::create($year, $month, 1, 0, 0, 0, 'UTC');
            $endDate = $startDate->copy()->endOfMonth();

            $queryByMonth = Transaction::where("user_id", $userId)
                ->whereBetween('currentDate', [$startDate, $endDate])
                ->limit(100)
                ->get()->map(function ($transaction) {
                    $transaction->income = ($transaction->income) ? "Ingreso" : "Gasto";
                    return $transaction;
                });
            return $queryByMonth;
        } catch (Exception $ex) {
            return response()->json(['error' => "Error al consultar tus gastos por mes"], 500);
        }
    }

    public function getTotalIngresosGastos($userId)
    {
        try {
            $yearCurrent = Carbon::now()->year;
            $transactions = Transaction::selectRaw('SUM(CASE WHEN income = 1 THEN amount ELSE 0 END) AS totalIncome, SUM(CASE WHEN income = 0 THEN amount ELSE 0 END) AS totalSpent')
                ->where('user_id', $userId)
                ->whereYear('currentDate', $yearCurrent)
                ->groupBy('user_id')
                ->first();
            $totalIncome = $transactions->totalIncome ?? 0;
            $totalSpent = $transactions->totalSpent ?? 0;
            $result = [
                'income' =>  number_format((float)$totalIncome, 2),
                'spent' => number_format((float)$totalSpent, 2)
            ];
            return $result;
        } catch (Exception $ex) {
            return response()->json(['error' => "Error al consultar tus ingresos e gastos"], 500);
        }
    }

    public function getChartTotalIngresosGastos($userId)
    {
        $yearCurrent = Carbon::now()->year;
        $transactions = Transaction::selectRaw('
        MONTH(currentDate) as month,
        SUM(CASE WHEN income = 1 THEN amount ELSE 0 END) as total_income,
        SUM(CASE WHEN income = 0 THEN amount ELSE 0 END) as total_spent')
            ->groupBy('month')
            ->orderBy('month')
            ->where('user_id', $userId)
            ->whereYear('currentDate', $yearCurrent)
            ->get();
        $monthlyDataIncome = array_fill(0, 12, 0);
        $monthlyDataSpent = array_fill(0, 12, 0);

        foreach ($transactions as $transaction) {
            $index = $transaction->month - 1;
            $monthlyDataIncome[$index] = round($transaction->total_income, 2);
            $monthlyDataSpent[$index] = round($transaction->total_spent, 2);
        }
        return [
            "gasto" => $monthlyDataIncome,
            "ingreso" => $monthlyDataSpent
        ];
    }

    public function getProductsMostSold($idUser){
        $listProducts = Product::withCount(['gastos'])
        ->withSum('gastos', 'amount')
        ->where("user_id", $idUser)
        ->having("gastos_count", ">", 0)
        ->orderBy('gastos_count', 'desc')
        ->take(5)
        ->get();
        return $listProducts;
    }
}
