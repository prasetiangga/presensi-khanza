<?php
 session_start();
 require_once('conf/command.php');
 require_once('conf/conf.php');
 require_once('conf/paging.php');
 header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // date in the past
 header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // always modified
 header("Cache-Control: no-store, no-cache, must-revalidate"); // HTTP/1.1
 header("Cache-Control: post-check=0, pre-check=0", false);
 header("Pragma: no-cache"); // HTTP/1.0
 $setting=  mysqli_fetch_array(bukaquery("select setting.nama_instansi,setting.alamat_instansi,setting.kabupaten,setting.propinsi,setting.kontak,setting.email,setting.logo from setting"));
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="refresh" content="180">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <title>Presensi Pegawai - RSIA Sayyidah</title>
    <link rel="icon" href="favicon.png" type="image/x-icon">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/font-awesome.css">
    <link rel="stylesheet" href="css/font-awesome-css.min.css">
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/webcam.js"></script>
    <!--<script src="js/webcam.min.js"></script>-->
    <script type="text/javascript" src="js/validator.js"></script>
    
    <style type="text/css">
        #results { padding: 0px; background:#EEFFEE; width: 490; height: 390 }
    </style>
    
    <style media="screen">
      body {
        position: absolute;
        top: 0; bottom: 0; left: 0; right: 0;
        height: 100%;
      }
      body:before {
        content: "";
        position: absolute;
        background: url('img/wallpaper.jpg');
        background-size: cover;
        z-index: -1; /* Keep the background behind the content */
        height: 20%; width: 20%; /* Using Glen Maddern's trick /via @mente */

        /* don't forget to use the prefixes you need */
        transform: scale(5);
        transform-origin: top left;
        filter: blur(2px);
      }
      .wrapper {
          text-align: center;
      }

      .button {
          position: absolute;
          top: 50%;
      }
      footer {
          position: fixed;
          right: 0px;
          bottom: 0px;
          height: 40px;
          width: calc(100% - 0px);
          font-size: 14px;
          color: #fff;
      }
      footer a, footer a:hover {
        color: #fff;
      }
      .btn-group .btn-fab{
          position: fixed !important;
          right: 20px;
          bottom: 60px;
      }
    </style>
    
    <script type="text/javascript">
        $(document).ready(function(){

            $("#sel_depart").change(function(){
                var deptid = $(this).val();

                $.ajax({
                    url: 'getJam.php',
                    type: 'post',
                    data: {depart:deptid},
                    dataType: 'json',
                    success:function(response){

                        var len = response.length;

                        $("#sel_jam").empty();
                        for( var i = 0; i<len; i++){
                            var dep_id = response[i]['dep_id'];
                            var jam_masuk = response[i]['jam_masuk'];
                            var jam_pulang = response[i]['jam_pulang'];

                            $("#sel_jam").append("<option value='"+jam_masuk+"'>"+jam_masuk+" - "+jam_pulang+"</option>");

                        }
                    }
                });
            });

        });
    </script>
</head>
<body>
<div class="container-fluid">
  <h1 class="display-3 text-center text-white m-3"><a href="https://192.168.2.7:40/presensi"><img class="logo" src="img/logo.png" alt="" width="100px"></a><br>SIKASA<br><font size="5">Sistem Informasi Kehadiran RSIA Sayyidah</font></h1>
  <div class="btn-group">
    <button class="btn btn-lg btn-danger btn-fab" id="main" data-toggle="modal" data-target="#myModal">
        <i class="fa fa-plus"></i> Presensi
    </button>
  </div>
</div>
<footer class="visible-lg visible-md bg-primary" style="padding:10px;z-index:1000;">
  <div class="canvas">
    <p class="pull-right">
      Made with <i class="fa fa-heart text-danger"></i> <a href="https://prasetia.pw/" target="_blank"><b>Angga P. Wijaya</b></a>
    </p>
    <p><i class="fa fa-fw fa-calendar"></i> <span><?php echo date('l, d M Y'); ?></span> <i class="fa fa-fw fa-clock-o"></i><span id="clock"></span></p>
  </div>
</footer>

<div id="post">
<form name="frmPresensi" id="frmPresensi" method="post" onsubmit="return validasiIsi();">
	
<!-- The Modal -->
<div class="modal" id="myModal" data-keyboard="false" data-backdrop="static">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close"><a href="">Tutup <span aria-hidden="true">&times;</span></a></button>
      </div>
      <!-- Modal body -->
      <div class="modal-body">
        <center>
          <div id="camera"></div><br>
          <input type="hidden" name="image" class="image-tag" onkeydown="setDefault(this, document.getElementById('MsgIsi1'));" id="TxtIsi1">
          <!-- Begin Departemen -->
          <select name="departemen" class="form-control input-lg" id="sel_depart" style="width:220px;">
			  <option>Unit</option>
				<?php 
				// Fetch Departemen
				$hasil=bukaquery("SELECT dep_id,nama FROM departemen");
				while($baris = mysqli_fetch_array($hasil) ){
					$departid = $baris['dep_id'];
					$depart_name = $baris['nama'];
				  
					// Option
					echo "<option value='".$departid."' >".$depart_name."</option>";
				}
				?> 
		  </select>
          <!-- End Departemen -->
          <br>
          <select name="jam_masuk" class="form-control input-lg" onkeydown="setDefault(this, document.getElementById('MsgIsi3'));" style="width:220px;" id="sel_jam" />
			  <option  id="TxtIsi3" value="">Masuk - Pulang</option>
		  </select>
		  <span id="MsgIsi3" style="color:#CC0000; font-size:15px;"></span><br>
          <div class="row" style="margin-bottom:30px;">
            <div class="col-md-3"></div>
            <div class="col-md-6">
              <input name="barcode" class="form-control input-lg" onkeydown="setDefault(this, document.getElementById('MsgIsi2'));" type="password" id="TxtIsi2" class="inputbox" value="" placeholder="NIP" maxlength="70"/>
              <span id="MsgIsi2" style="color:#CC0000; font-size:15px;"></span>
            </div>
            <div class="col-md-3"></div>
          </div>
          <input name=BtnSimpan type=submit class="btn btn-info btn-lg" value="Simpan" onClick="take_snapshot()"/>
          <input type="hidden" name="image" class="image-tag">
        </center>
      </div>
    </div>
  </div>
</div>

</form>
</div>
	<script language="JavaScript">
			Webcam.set({
				width: 300,
				height: 370,
				image_format: 'jpeg',
				jpeg_quality: 90,
				camera_mirror: false
			});
			
			Webcam.attach( '#camera' );
			
			function take_snapshot() {
				Webcam.snap( function(data_uri) {
					$(".image-tag").val(data_uri);
					document.getElementById('results').innerHTML = '<img src="'+data_uri+'"/>';
				});
			}
	</script>
<!--
<script>
      $('#myModal').on('show.bs.modal', function() {

          Webcam.set({
              width:240,
              height:240,
              image_format:'jpeg',
              jpeg_quality:90,
          });
          Webcam.attach('#camera');
          
          function take_snapshot() {
                    Webcam.snap( function(data_uri) {
                        $(".image-tag").val(data_uri);
                        document.getElementById('results').innerHTML = '<img src="'+data_uri+'"/>';
                    });
                };
      });
</script>
-->

<!--
<script type="text/javascript">
  $(".alert-dismissible").fadeTo(3000, 500).slideUp(500);
</script>
-->

<?php
                $BtnSimpan=isset($_POST['BtnSimpan'])?$_POST['BtnSimpan']:NULL;
                if (isset($BtnSimpan)) {
                    $jam_masuk      = trim($_POST['jam_masuk']);  
                    $barcode        = trim($_POST['barcode']);
                    
                    $_sqlbar        = "select id from barcode where barcode='$barcode'";
                    $hasilbar       = bukaquery($_sqlbar);
                    @$barisbar       = mysqli_fetch_array($hasilbar);  
                    @$idpeg          = $barisbar["id"];
                    
                    $_sqljamdatang  = "select jam_jaga.shift,CURRENT_DATE() as hariini,pegawai.departemen from jam_jaga inner join pegawai on pegawai.departemen=jam_jaga.dep_id 
                                       where jam_jaga.jam_masuk='$jam_masuk' and pegawai.id='$idpeg'";
                    $hasiljamdatang = bukaquery($_sqljamdatang);
                    @$barisjamdatang = mysqli_fetch_array($hasiljamdatang);  
                    @$shift          = $barisjamdatang["shift"];
                    @$hariini        = $barisjamdatang["hariini"];
                    @$departemen     = $barisjamdatang["departemen"];
                    
                    $_sqlketerlambatan = "select * from set_keterlambatan";
                    $hasilketerlmabatan=  bukaquery($_sqlketerlambatan);
                    @$barisketerlambatan=  mysqli_fetch_array($hasilketerlmabatan);
                    @$toleransi      = $barisketerlambatan[0];
                    @$terlambat1     = $barisketerlambatan[1];
                    @$terlambat2     = $barisketerlambatan[2];
                    
                    /*tambahan ngambil foto*/
                    /*
                    $_sqlfoto	= "SELECT photo FROM temporary_presensi";
                    $hasilfoto	= bukaquery($_sqlfoto);
                    $barisfoto	= mysqli_fetch_array($hasilfoto);
                    $foto		= $barisfoto[0];
                    */
                    
                    if(file_exists(host()."presensi/".$hariini.$shift.$idpeg.".jpeg")){
                        @unlink(host()."presensi/".$hariini.$shift.$idpeg.".jpeg");
                    }
                    
                    /*Pilih salah satu*/
                    /*Nama file tidak ditambahkan jam pada akhir file*/
                    /*
                    @$img            = $_POST["image"];
                    @$image_parts    = explode(";base64,", $img);
                    @$image_type_aux = explode("image/", $image_parts[0]);
                    @$image_type     = $image_type_aux[1];
                    @$image_base64   = base64_decode($image_parts[1]);
                    @$file           = $hariini.$shift.$idpeg.".jpeg";
                    @file_put_contents($file, $image_base64);
                    */
                    /*End nama file tidak ditambahkan jam pada akhir file*/
                    
                    /*Nama file ditambahkan jam pada akhir file*/
                    /*
                    @$img            = $_POST["image"];
                    @$folderPath 	 = "upload/";
                    @$image_parts    = explode(";base64,", $img);
                    @$image_type_aux = explode("image/", $image_parts[0]);
                    @$image_type     = $image_type_aux[1];
                    @$image_base64   = base64_decode($image_parts[1]);
                    @$file           = $hariini.$shift.$idpeg.date("h:i:sa").".jpeg";
                    @$filePath		 = $folderPath . $file;
                    //@$file           = $hariini.$shift.$idpeg.".jpeg";
                    @file_put_contents($filePath, $image_base64);
                    */
                    /*End nama file ditambahkan jam pada akhir file*/
                    
                    //echo "Jam Masuk : ".$jam_masuk." ID : ".$idpeg."departemen : $departemen  Shift : $shift";
                    
                    $jam="now()";
                    if(!empty($jam_masuk)){
                        $jam="CONCAT(CURRENT_DATE(),' $jam_masuk')";
                    }
                    
                    $_sqlvalid         = "select id from rekap_presensi where id='$idpeg' and shift='$shift' and jam_datang like '%$hariini%'";
                    $hasilvalid        = bukaquery($_sqlvalid);
                    @$barisvalid       = mysqli_fetch_array($hasilvalid);  
                    @$idvalid          = $barisvalid["id"];  
                    
                    if(!empty($idvalid)){
                        echo"<p style='text-align:center;'><b><font size='7' color='#db0214'>Anda sudah presensi untuk tanggal<br>".date('Y-m-d')."</font></b></p><html><head><title></title><meta http-equiv='refresh' content='5;URL=?page=Input'></head><body></body></html>";
                    }elseif((!empty($idpeg))&&(!empty($shift))&&(empty($idvalid))) {
                        $_sqlcek         = "select id, shift, jam_datang, jam_pulang, status, keterlambatan, durasi, photo from temporary_presensi where id='$idpeg'";
                        $hasilcek        = bukaquery($_sqlcek);
                        @$bariscek       = mysqli_fetch_array($hasilcek);  
                        @$idcek          = $bariscek["id"];         
                        
                        
                        if(empty($idcek)){
                            if(empty($img)){
                                echo "<p style='text-align:center;'><b><font size='6' color='#db0214'>Pilih SHIFT dahulu!</font></b></p>";
                            }else{
                                Tambah2("temporary_presensi","'$idpeg','$shift',NOW(),NULL,
                                if(TIME_TO_SEC(now())-TIME_TO_SEC($jam)>($toleransi*60),if(TIME_TO_SEC(now())-TIME_TO_SEC($jam)>($terlambat1*60),if(TIME_TO_SEC(now())-TIME_TO_SEC($jam)>($terlambat2*60),'Terlambat II','Terlambat I'),'Terlambat Toleransi'),'Tepat Waktu'),
                                if(TIME_TO_SEC(now())-TIME_TO_SEC($jam)>($toleransi*60),SEC_TO_TIME(TIME_TO_SEC(now())-TIME_TO_SEC($jam)),''),'','$file'", 
                                " Presensi Masuk jam $jam_masuk ".getOne("select if(TIME_TO_SEC(now())-TIME_TO_SEC($jam)>($toleransi*60),concat('<br>Keterlambatan ',SEC_TO_TIME(TIME_TO_SEC(now())-TIME_TO_SEC($jam))),'')"));
                                echo"<html><head><title></title><meta http-equiv='refresh' content='5;URL=?page=Input'></head><body><center><img src='upload/$file' width='200' height='270' /></center></body></html>";
                            }                            
                        }elseif(!empty($idcek)){  
                            $jamdatang=getOne("select jam_jaga.jam_masuk from jam_jaga inner join pegawai on pegawai.departemen=jam_jaga.dep_id where jam_jaga.shift='$shift' and pegawai.id='$idcek'");
                            $jampulang=getOne("select jam_jaga.jam_pulang from jam_jaga inner join pegawai on pegawai.departemen=jam_jaga.dep_id where jam_jaga.shift='$shift' and pegawai.id='$idcek'");

                            $jam="now()";
                            if(!empty($jamdatang)){
                                $jam="CONCAT(CURRENT_DATE(),' $jamdatang')";
                            }
                            $jam2="now()";
                            if(!empty($jampulang)){
                                 $jam2="CONCAT(CURRENT_DATE(),' $jampulang')";
                            }
                            $masuk=getOne("select jam_datang from temporary_presensi where id='$idcek'");
                            $pulang="now()";

                            Ubah2(" temporary_presensi "," jam_pulang=NOW(),status=if(TIME_TO_SEC('$masuk')-TIME_TO_SEC($jam)>($toleransi*60),if(TIME_TO_SEC('$masuk')-TIME_TO_SEC($jam)>($terlambat1*60),if(TIME_TO_SEC('$masuk')-TIME_TO_SEC($jam)>($terlambat2*60),
                                   concat('Terlambat II',if(TIME_TO_SEC($pulang)-TIME_TO_SEC($jam2)<0,' & PSW',' ')),concat(', Terlambat I',if(TIME_TO_SEC($pulang)-TIME_TO_SEC($jam2)<0,' & PSW',' '))),
                                   concat('Terlambat Toleransi',if(TIME_TO_SEC($pulang)-TIME_TO_SEC($jam2)<0,' & PSW',' '))),concat('Tepat Waktu',if(TIME_TO_SEC($pulang)-TIME_TO_SEC($jam2)<0,' & PSW',' '))),
                                   durasi=(SEC_TO_TIME(unix_timestamp(now()) - unix_timestamp(jam_datang))) where id='$idpeg'  ");                            
                            $_sqlcek        = "select id, shift, jam_datang, jam_pulang, status, keterlambatan, durasi, photo from temporary_presensi where id='$idpeg'";
                            $hasilcek       = bukaquery($_sqlcek);
                            $bariscek       = mysqli_fetch_array($hasilcek);  
                            $idcek          = $bariscek["id"];                                                      
                            $shift          = $bariscek["shift"];
                            $jam_datang     = $bariscek["jam_datang"];
                            $jam_pulang     = $bariscek["jam_pulang"];
                            $status         = $bariscek["status"];
                            $keterlambatan  = $bariscek["keterlambatan"];
                            $durasi         = $bariscek["durasi"];
                            Tambah2("rekap_presensi","'$idcek','$shift','$jam_datang','$jam_pulang','$status','$keterlambatan','$durasi','','$file'", " Presensi Pulang <br>$jam_pulang" );
                            hapusinput(" delete from temporary_presensi where id ='$idcek' ");
                            echo"<html><head><title></title><meta http-equiv='refresh' content='5;URL=?page=Input'></head><body><center><img src='upload/$file' width='200' height='270' /></center></body></html>";
                        } 
                    }elseif (empty($idpeg)||empty($shift)){
                        echo "<p style='text-align:center;'><b><font size='6' color='#db0214'>Jam Masuk Unit atau NIP salah!<br>Silahkan pilih berdasarkan shift UNIT anda!</font></b></p>";
                    }
                }
            ?>
            
	<script>
		function showTime() {
			var today = new Date();
			var curr_hour = today.getHours();
			var curr_minute = today.getMinutes();
			var curr_second = today.getSeconds();
			curr_hour = checkTime(curr_hour);
			curr_minute = checkTime(curr_minute);
			curr_second = checkTime(curr_second);
			document.getElementById('clock').innerHTML=curr_hour + ":" + curr_minute + ":" + curr_second;
			}
			
		function checkTime(i) {
			if (i < 10) {
				i = "0" + i;
				}
				return i;
				}
				setInterval(showTime, 500);
	</script>
</body>
</html>
