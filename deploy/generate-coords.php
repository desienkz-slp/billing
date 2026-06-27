<?php
/*   __________________________________________________
    |  Obfuscated by YAK Pro - Php Obfuscator  3.0.0   |
    |              on 2026-06-26 22:34:39              |
    |    GitHub: https://github.com/pk-fr/yakpro-po    |
    |__________________________________________________|
*/
 goto UUfWd; Ad3CI: $OcqYk = 0; goto iJssC; iJssC: $YxcPx = 0; goto UGSGV; UGSGV: $ZKt2Q = 0; goto TwfVw; Nh0a3: $RQQSe = App\Models\Customer::all(); goto Ad3CI; TwfVw: foreach ($RQQSe as $ULrP7) { goto jElrC; jElrC: $l3cCu = $JuZXp + $OcqYk * $FqfBs; goto T2kOz; kFwpF: $YxcPx++; goto H_o1M; Kc2sr: App\Models\CustomerCoordinate::updateOrCreate(['customer_id' => $ULrP7->id], ['tenant_id' => $ULrP7->tenant_id, 'latitude' => round($l3cCu, 7), 'longitude' => round($iNfWo, 7)]); goto kFwpF; Mf9UK: $ZKt2Q++; goto JFyfX; H_o1M: if ($YxcPx >= 4) { $YxcPx = 0; $OcqYk++; } goto Mf9UK; mUhna: $iNfWo += mt_rand(-5, 5) / 10000; goto Kc2sr; T2kOz: $iNfWo = $rpibv + $YxcPx * $FqfBs; goto vYlli; vYlli: $l3cCu += mt_rand(-5, 5) / 10000; goto mUhna; JFyfX: } goto JIQKb; j1oJK: $rpibv = 112.285; goto TVbys; UUfWd: $JuZXp = -8.145; goto j1oJK; TVbys: $FqfBs = 0.0025; goto Nh0a3; JIQKb: echo "OK! 18 Koordinat berhasil diset di Desa Jatitengah, Selopuro, Blitar.\n";
