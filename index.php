<?php
	//Ambil data dari file csv dan ubah ke array
    $theData = array();
	$filename = "DataTugas2.csv";
	if (($handle = fopen($filename, "r")) !== FALSE) {
		$key = 0;
		while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
			$count = count($data);
			for ($i=0; $i < $count; $i++) {
				$theData[$key][$i] = $data[$i];
			}
			$key++;
		}
		fclose($handle);
	}
	$theData[0][3] = 'Score';
	
	//Fungsi Trapesium
	function trapesium($x, $a, $b, $c, $d){
		if($x < $a || $x > $d){
			return -1; //di luar grafik
		}else if($a <= $x && $x < $b){
			return ($x - $a)/($b - $a);
		}else if($b <= $x && $x <= $c){
			return 1;
		}else if($c < $x && $x <= $d){
			return ($d - $x)/($d - $c);
		}
	}
	
	//Membership Function Pendapatan
	function MembershipPendapatan($x){
		$i = -1;
		$kategori = array();
		$s = Trapesium($x, 0, 0, 0.3, 0.55);
		$as =Trapesium($x, 0.35, 0.55, 0.9, 1.15);
		$ab = Trapesium($x, 1, 1.27, 1.48, 1.54);
		$b = Trapesium($x, 1.42, 1.7, 2, 2);
		if($s >= 0){
			$i++;
			$kategori[$i][0] = $s;
			$kategori[$i][1] = 'Sedikit';
		}
		if($as >= 0){
			$i++;
			$kategori[$i][0] = $as;
			$kategori[$i][1] = 'Agak Sedikit';
		}
		if($ab >= 0){
			$i++;
			$kategori[$i][0] = $ab;
			$kategori[$i][1] = 'Agak Banyak';
		}
		if($b >= 0){
			$i++;
			$kategori[$i][0] = $b;
			$kategori[$i][1] = 'Banyak';
		}
		return $kategori;
	}
	
	//Membership Function Hutang
	function MembershipHutang($x){
		$i = -1;
		$kategori = array();
		$s = Trapesium($x, 0, 0, 20, 30);
		$as =Trapesium($x, 24, 31, 38, 53);
		$ab = Trapesium($x, 41, 56, 66, 79);
		$b = Trapesium($x, 78, 85, 100, 100);
		if($s >= 0){
			$i++;
			$kategori[$i][0] = $s;
			$kategori[$i][1] = 'Sedikit';
		}
		if($as >= 0){
			$i++;
			$kategori[$i][0] = $as;
			$kategori[$i][1] = 'Agak Sedikit';
		}
		if($ab >= 0){
			$i++;
			$kategori[$i][0] = $ab;
			$kategori[$i][1] = 'Agak Banyak';
		}
		if($b >= 0){
			$i++;
			$kategori[$i][0] = $b;
			$kategori[$i][1] = 'Banyak';
		}
		return $kategori;
	}
	
	//Rule (Nilai Acceptance)
	function rule($pendapatan, $hutang){
		if($pendapatan == 'Sedikit' && $hutang == 'Sedikit'){return 'Probably Not';}
		else if($pendapatan == 'Sedikit' && $hutang == 'Agak Sedikit'){return 'Probably';}
		else if($pendapatan == 'Sedikit' && $hutang == 'Agak Banyak'){return 'Yes';}
		else if($pendapatan == 'Sedikit' && $hutang == 'Banyak'){return 'Yes';}
		else if($pendapatan == 'Agak Sedikit' && $hutang == 'Sedikit'){return 'No';}
		else if($pendapatan == 'Agak Sedikit' && $hutang == 'Agak Sedikit'){return 'Probably Not';}
		else if($pendapatan == 'Agak Sedikit' && $hutang == 'Agak Banyak'){return 'Probably';}
		else if($pendapatan == 'Agak Sedikit' && $hutang == 'Banyak'){return 'Yes';}
		else if($pendapatan == 'Agak Banyak' && $hutang == 'Sedikit'){return 'No';}
		else if($pendapatan == 'Agak Banyak' && $hutang == 'Agak Sedikit'){return 'No';}
		else if($pendapatan == 'Agak Banyak' && $hutang == 'Agak Banyak'){return 'Probably Not';}
		else if($pendapatan == 'Agak Banyak' && $hutang == 'Banyak'){return 'Probably';}
		else if($pendapatan == 'Banyak' && $hutang == 'Sedikit'){return 'No';}
		else if($pendapatan == 'Banyak' && $hutang == 'Agak Sedikit'){return 'No';}
		else if($pendapatan == 'Banyak' && $hutang == 'Agak Banyak'){return 'No';}
		else if($pendapatan == 'Banyak' && $hutang == 'Banyak'){return 'Probably Not';}
	}
	
	//Fuzifikasi & Inferensi
	for($i = 1;$i <= 100;$i++){
		//Memisahkan data ke kategori (Fuzifikasi)
		$pendapatan = MembershipPendapatan($theData[$i][1]);
		$hutang = MembershipHutang($theData[$i][2]);
		//Hasil acceptance [dari rule][per kategori data] & nilai minimum hasil fungsi [per kategori data] (Fuzifikasi & Inferensi)
		$l = -1;
		$acc = array();
		$npendapatan = sizeof($pendapatan);
		if($npendapatan > 0){
			for($j = 0;$j < $npendapatan;$j++){
				$minPendapatan = $pendapatan[$j][0];
				$nhutang = sizeof($hutang);
				for($k = 0;$k < $nhutang;$k++){
					$minHutang = $hutang[$k][0];
					if($minHutang < $minPendapatan){
						$min = $minHutang;
					}else{
						$min = $minPendapatan;
					}
					$l++;
					/*echo $theData[$i][1] . ' - ' . $theData[$i][2] . '<br>';
					echo $pendapatan[$j][0] . ' - ' . $hutang[$k][0] . '<br>';
					echo 'Min : ' . $min . '<br>';
					echo $pendapatan[$j][1] . ' - ' . $hutang[$k][1] . '<br>';*/
					$acc[$l][0] = rule($pendapatan[$j][1], $hutang[$k][1]); //Hasil acceptance
					//echo 'Acceptance : ' . $acc[$l][0] . '<br>';
					$acc[$l][1] = $min; //Nilai fungsi minimum
				}
			}
		}
		$m = -1;
		$accept = array();
		//Memisahkan per kategori acceptance [hanya 1 nilai per kategori] (Inferensi)
		for($z = 0;$z < sizeof($acc);$z++){
			if($m < 0){
				$m++;
				$accept[$m][0] = $acc[$z][0]; //Hasil acceptance
				$accept[$m][1] = $acc[$z][1]; //Nilai Fungsi
			}else{
				for($x = 0;$x <= 0;$x++){
					if($acc[$x][0] != $acc[$z][0]){
						$m++;
						$accept[$m][0] = $acc[$z][0]; //Hasil acceptance
						$accept[$m][1] = $acc[$z][1]; //Nilai Fungsi
					}
				}
			}
			//echo $acc[$z][0] . ' - ' . $acc[$z][1] . '<br>';
		}
		//Mencari nilai maksimum per kategori acceptance [1 nilai tsb dibandingkan dengan semua nilai] (Inferensi)
		for($y = 0;$y < sizeof($accept);$y++){
			for($x = 0;$x < sizeof($acc);$x++){
				if($accept[$y][0] == $acc[$x][0]){
					if($acc[$x][1] > $accept[$y][1]){
						$accept[$y][1] = $acc[$x][1];
					}
				}
			}
		}
		/*for($a = 0;$a < sizeof($accept);$a++){
			echo $accept[$a][0] . ' - ' . $accept[$a][1] . '<br>';
		}*/
		
		//Defuzifikasi
		$totalup = 0;
		$totalbottom = 0;
		for($w = 0;$w < sizeof($accept);$w++){
			if($accept[$w][0] == 'No'){
				$totalup = $totalup + (35 * $accept[$w][1]);
				$totalbottom = $totalbottom + $accept[$w][1];
			}else if($accept[$w][0] == 'Probably Not'){
				$totalup = $totalup + (55 * $accept[$w][1]);
				$totalbottom = $totalbottom + $accept[$w][1];
			}else if($accept[$w][0] == 'Probably'){
				$totalup = $totalup + (70 * $accept[$w][1]);
				$totalbottom = $totalbottom + $accept[$w][1];
			}else if($accept[$w][0] == 'Yes'){
				$totalup = $totalup + (95 * $accept[$w][1]);
				$totalbottom = $totalbottom + $accept[$w][1];
			}
		}
		$theData[$i][3] = $totalup / $totalbottom;
		//echo 'Score : ' . $theData[$i][3];
	}
	
	//Sorting score data
	$n = 100;
    do {
        $swapped = false;
        for ($i = 1; $i < $n; $i++) {
            if ($theData[$i][3] < $theData[$i + 1][3]) {
                $temp = $theData[$i];
                $theData[$i] = $theData[$i + 1];
                $theData[$i + 1] = $temp;
                $swapped = true;
            }
        }
        $n--;
    }while ($swapped);
	
	//Prepare output array
	$out = array();
	for($q = 1;$q <= 20;$q++){
		$i = $q - 1;
		$out[$i][0] = $theData[$q][0];
		//echo $theData[$q][0] . ' - ' . ($theData[$q][1]) . ' - ' . ($theData[$q][2]) . ' - ' . (($theData[$q][2])/($theData[$q][1])) . ' - ' . $theData[$q][3] . '<br>';
	}
	
	//Export to CSV
	$output = fopen("php://output",'w') or die("Can't open php://output");
	header("Content-Type:application/csv"); 
	header("Content-Disposition:attachment;filename=TebakanTugas2.csv");
	foreach($out as $outp) {
		fputcsv($output, $outp);
	}
	fclose($output) or die("Can't close php://output");
?> 