<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dividend;
use App\Models\Announcement;
use Carbon\Carbon;

class RealDividendSeeder extends Seeder
{
    public function run(): void
    {
        $dividends = [
            ['symbol' => 'NAMU.N0000', 'amount' => 2.00, 'xd' => '2026-04-10', 'company' => 'NAMUNUKULA PLANTATIONS PLC'],
            ['symbol' => 'KGAL.N0000', 'amount' => 3.00, 'xd' => '2026-04-10', 'company' => 'KEGALLE PLANTATIONS PLC'],
            ['symbol' => 'COCR.N0000', 'amount' => 3.00, 'xd' => '2026-04-16', 'company' => 'COMMERCIAL CREDIT AND FINANCE PLC'],
            ['symbol' => 'OSEA.N0000', 'amount' => 1.75, 'xd' => '2026-04-29', 'company' => 'OVERSEAS REALTY (CEYLON) PLC'],
            ['symbol' => 'AFSL.N0000', 'amount' => 2.72, 'xd' => '2026-05-04', 'company' => 'ABANS FINANCE PLC'],
            ['symbol' => 'CHL.N0000', 'amount' => 51.00, 'xd' => '2026-05-19', 'company' => 'CEYLINCO HOLDINGS PLC'],
            ['symbol' => 'CTC.N0000', 'amount' => 17.54, 'xd' => '2026-05-26', 'company' => 'CEYLON TOBACCO COMPANY PLC'],
            ['symbol' => 'SLTL.N0000', 'amount' => 0.75, 'xd' => '2026-06-19', 'company' => 'SRI LANKA TELECOM PLC'],
        ];

        foreach ($dividends as $div) {
            // Create processed dividend
            Dividend::updateOrCreate(
                ['symbol' => $div['symbol'], 'xd_date' => $div['xd']],
                [
                    'amount_per_share' => $div['amount'],
                    'type' => 'Cash Dividend',
                ]
            );

            // Create announcement record
            Announcement::updateOrCreate(
                ['symbol' => $div['symbol'], 'date' => Carbon::parse($div['xd'])->subDays(14)->format('Y-m-d')],
                [
                    'announcement_type' => 'Dividend',
                    'raw_text' => "{$div['company']} announced a Cash Dividend of Rs. {$div['amount']} per share. XD Date: {$div['xd']}.",
                ]
            );
        }
    }
}
