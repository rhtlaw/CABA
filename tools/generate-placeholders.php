<?php
// Usage: php tools/generate-placeholders.php wp-content/themes/apba-child/assets/images
// Generates neutral, text-free placeholder images (navy/blue corporate gradients with skyline lines).
function grad($w,$h,$c1,$c2,$file,$skyline=true,$seed=1){
  mt_srand($seed);
  $im=imagecreatetruecolor($w,$h);
  for($y=0;$y<$h;$y++){ for($x=0;$x<$w;$x+=4){
    $t=($x/$w*0.6+$y/$h*0.4);
    $r=(int)($c1[0]+($c2[0]-$c1[0])*$t);$g=(int)($c1[1]+($c2[1]-$c1[1])*$t);$b=(int)($c1[2]+($c2[2]-$c1[2])*$t);
    imagefilledrectangle($im,$x,$y,$x+3,$y,imagecolorallocate($im,$r,$g,$b));
  }}
  if($skyline){
    $x=0; $base=$h; 
    while($x<$w){ $bw=mt_rand((int)($w/40),(int)($w/14)); $bh=mt_rand((int)($h*0.15),(int)($h*0.55));
      $a=imagecolorallocatealpha($im,255,255,255,mt_rand(108,118));
      imagefilledrectangle($im,$x,$base-$bh,$x+$bw,$base,$a);
      // windows
      $wc=imagecolorallocatealpha($im,255,255,255,100);
      for($wy=$base-$bh+8;$wy<$base-6;$wy+=max(6,(int)($h/60))) for($wx=$x+5;$wx<$x+$bw-5;$wx+=max(7,(int)($w/160))) if(mt_rand(0,3)==0) imagefilledrectangle($im,$wx,$wy,$wx+2,$wy+3,$wc);
      $x+=$bw+mt_rand(2,(int)($w/120)); }
  }
  // subtle network arcs
  $lc=imagecolorallocatealpha($im,255,255,255,112);
  imagesetthickness($im,2);
  for($i=0;$i<5;$i++){ imagearc($im,(int)($w*mt_rand(20,80)/100),(int)($h*1.1),(int)($w*(0.6+$i*0.25)),(int)($h*(0.9+$i*0.3)),180,360,$lc); }
  imagejpeg($im,$file,82); imagedestroy($im);
}
$d=$argv[1];
grad(1920,820,[6,28,61],[24,78,140],"$d/hero-placeholder.jpg",true,3);
grad(1920,420,[8,32,70],[20,64,120],"$d/banner-placeholder.jpg",true,7);
grad(900,640,[20,64,120],[10,42,92],"$d/about-placeholder.jpg",false,11);
grad(1200,675,[120,16,36],[10,42,92],"$d/summit-placeholder.jpg",true,5);
grad(1200,675,[10,42,92],[40,96,160],"$d/post-placeholder.jpg",false,9);
// Portrait silhouette placeholder.
$im=imagecreatetruecolor(800,1000);
for($y=0;$y<1000;$y++){ $t=$y/1000; imageline($im,0,$y,799,$y,imagecolorallocate($im,(int)(226-20*$t),(int)(232-20*$t),(int)(240-16*$t))); }
$c=imagecolorallocate($im,170,182,199);
imagefilledellipse($im,400,380,300,340,$c);
imagefilledellipse($im,400,1000,640,560,$c);
imagejpeg($im,"$d/portrait-placeholder.jpg",82);
