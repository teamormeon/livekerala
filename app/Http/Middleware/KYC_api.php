<?php

namespace App\Http\Middleware;

use App\Models\Kyc as KYCModel;
use App\Models\UserKyc;
use App\Traits\ApiResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class KYC_api
{
    use ApiResponse;
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $kycTypes = KYCModel::where('status',1)->pluck('id');
        $userKyc = UserKyc::where('user_id', Auth::user()->id)->where('status',1)->whereIn('kyc_id', $kycTypes)->get();
        $userKycIds = $userKyc->pluck('kyc_id')->toArray();
        $missingKycTypes = array_diff($kycTypes->toArray(), $userKycIds);

        if (!empty($missingKycTypes)) {
            return response()->json($this->withError('Some KYC types are missing for the user'));
        }
        return $next($request);
    }

}
