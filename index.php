<?php
function getBrowser()
{
    $u_agent  = $_SERVER['HTTP_USER_AGENT'];
    $bname    = 'Unknown';
    $platform = 'Unknown';
    $version  = "";
    
    //First get the platform?
    if (preg_match('/linux/i', $u_agent)) {
        $platform = 'linux';
    } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
        $platform = 'mac';
    } elseif (preg_match('/windows|win32/i', $u_agent)) {
        $platform = 'windows';
    }
    
    // Next get the name of the useragent yes seperately and for good reason
    if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
        $bname = 'Internet Explorer';
        $ub    = "MSIE";
    } elseif (preg_match('/Firefox/i', $u_agent)) {
        $bname = 'Mozilla Firefox';
        $ub    = "Firefox";
    } elseif (preg_match('/Chrome/i', $u_agent)) {
        $bname = 'Google Chrome';
        $ub    = "Chrome";
    } elseif (preg_match('/Safari/i', $u_agent)) {
        $bname = 'Apple Safari';
        $ub    = "Safari";
    } elseif (preg_match('/Opera/i', $u_agent)) {
        $bname = 'Opera';
        $ub    = "Opera";
    } elseif (preg_match('/Netscape/i', $u_agent)) {
        $bname = 'Netscape';
        $ub    = "Netscape";
    }
    
    // finally get the correct version number
    $known   = array(
        'Version',
        $ub,
        'other'
    );
    $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    if (!preg_match_all($pattern, $u_agent, $matches)) {
        // we have no matching number just continue
    }
    
    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        //we will have two since we are not using 'other' argument yet
        //see if version is before or after the name
        if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
            $version = $matches['version'][0];
        } else {
            $version = $matches['version'][1];
        }
    } else {
        $version = $matches['version'][0];
    }
    
    // check if we have a number
    if ($version == null || $version == "") {
        $version = "?";
    }
    
    return array(
        'userAgent' => $u_agent,
        'name' => $bname,
        'version' => $version,
        'platform' => $platform,
        'pattern' => $pattern
    );
}

// now try it


function ip_visitor_country()
{
    
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];
    $country = "Unknown";
    
    if (filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
        $ip = $forward;
    } else {
        $ip = $remote;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://www.geoplugin.net/json.gp?ip=" . $ip);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $ip_data_in = curl_exec($ch); // string
    curl_close($ch);
    
    $ip_data = json_decode($ip_data_in, true);
    $ip_data = str_replace('&quot;', '"', $ip_data); // for PHP 5.2 see stackoverflow.com/questions/3110487/
    
    if ($ip_data && $ip_data['geoplugin_countryName'] != null) {
        $country = $ip_data['geoplugin_countryName'];
    }
    
    return $country;
}

$ua       = getBrowser();
$browser  = $ua['name'];
$platform = $ua['platform'];
$country  = ip_visitor_country();


function getUserIP()
{
    $client  = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote  = $_SERVER['REMOTE_ADDR'];
    
    if (filter_var($client, FILTER_VALIDATE_IP)) {
        $ip = $client;
    } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
        $ip = $forward;
    } else {
        $ip = $remote;
    }
    
    return $ip;
}

$user_ip = getUserIP();

// Connects to your Database 
mysql_connect("localhost", "fedhadvi_user", "fedha12345") or die(mysql_error());
mysql_select_db("fedhadvi_stats") or die(mysql_error());

mysql_query("insert into counter(browser,counter,country,ip,platform,time)
      values('$browser',1,'$country','$user_ip','$platform',(select now() from dual))
      ");

//Retrieves the current count

$count = mysql_fetch_row(mysql_query("SELECT sum(counter) FROM  counter"));

//Displays the count on your site

// print "$count[0]";

?>
 <!DOCTYPE html>
<html lang="en">
   <head>
      <title>Fedha Advisory</title>
	  <link rel="icon" href="favicon1.png" type="image/png">
      <link rel="shortcut icon" href="favicon.ico" type="img/x-icon">
      <meta charset="utf-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <link rel="stylesheet" type="text/css" href="css/bootstrap.css">
      <link rel="stylesheet" type="text/css" href="css/font-awesome.css">
      <link rel='stylesheet' id='camera-css'  href='css/camera.css' type='text/css' media='all'>
      <link rel="stylesheet" type="text/css" href="css/slicknav.css">
      <link rel="stylesheet" href="css/prettyPhoto.css" type="text/css" media="screen" title="prettyPhoto main stylesheet" charset="utf-8" />
      <link rel="stylesheet" type="text/css" href="css/style.css">
      <script type="text/javascript" src="js/jquery-1.8.3.min.js"></script>
      <link href='http://fonts.googleapis.com/css?family=Roboto:400,300,700|Open+Sans:700' rel='stylesheet' type='text/css'>
      <script type="text/javascript" src="js/jquery.mobile.customized.min.js"></script>
      <script type="text/javascript" src="js/jquery.easing.1.3.js"></script> 
      <script type="text/javascript" src="js/camera.min.js"></script>
      <script type="text/javascript" src="js/myscript.js"></script>
      <script src="js/sorting.js" type="text/javascript"></script>
      <script src="js/jquery.isotope.js" type="text/javascript"></script>
						
      <script>
         jQuery(function(){
         		jQuery('#camera_wrap_1').camera({ 
         		transPeriod: 500,
         		time: 3000,
         		height: '490px',
         		thumbnails: false,
         		pagination: true,
         		playPause: false,
         		loader: false,
         		navigation: false,
         		hover: false
         	});
         });
      </script>
   </head>
   <body>
   <div id="fb-root"></div>
<script>
  window.fbAsyncInit = function() {
    FB.init({
      appId            : '145448989394299',
      autoLogAppEvents : true,
      xfbml            : true,
      version          : 'v2.10'
    });
    FB.AppEvents.logPageView();
  };

  (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
</script>

      <!--home start-->
      <div id="home" class="cushycms"><div class="default" id="menuF">
<div class="container">
<div class="row">
<div class="logo col-md-4">
<div><a href="#"><img alt="Logotipo" src="images/logo.jpg" /> </a></div>
</div>

<div class="col-md-8">
<div class="navmenu" style="text-align: center; background-color:white">
<ul id="menu">
	<li><b>About Us</b></li>
	<li>What we Do</li>
	<li><a href="#portfolio"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Portifolio</font></font></a></li>
	<li><a href="#clients-partners"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Clients e Pa</font></font></a>tnerships</li>
	<li class="last"><a href="#contacts"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Conta</font></font></a>ct Us</li>
</ul>
</div>
</div>
</div>
</div>
</div>
</div>
      <!--about start-->    
      <div id="theFirm">
<section class="business-talking"><!--business-talking-start-->
	<div class="container">
        <h2>About us</h2> 
    </div>
</section>
         <div class="container">
            <div class="row">
               <div class="col-md-2 wwa"></div>
               <div class="col-md-8 project">
                  <span name="about" ></span>
                  <h2 style="text-align:justify" class="cushycms">Who We Are?</h2>
                  <p class="cushycms" style="text-align:justify"><p style="text-align: justify;">Fedha Advisory (FEDHA) is a Mozambican company, operating in Mozambique (for now) for five years (5) and focused on Asset (Equipment) Trade Financing. Asset Trade Financing: In Asset (Equipment) Trade Financing FEDHA offers Two (2) main services to its clients (partners) namely:</p>

<p style="text-align: justify;"><br />
<strong>PROCUREMENT (ASSETS):</strong> Global Asset (Equipment) Procurement, to large and established mid-sized companies (in Mozambique)</p>

<p class="cushycms" style="text-align: justify;"><strong>Financial Package (solution):</strong> Providing a Financial Packages (solutions) to acquire the Assets (equipment), procured, serviced and sold by Fedha to the specific client and partner<br />
<br />
<strong>Note: There is currently a need for this service and solution, and there is no other company doing this integrated service in Mozambique.</strong><br />
<em><a href="file:///C:/Users/User/AppData/Local/Microsoft/Windows/INetCache/Content.Outlook/0ZFQ6IZB/FA%20Asset%20Trade%20Finance_Presentation%20Local_20211208_Updated.pdf" target="_blank"><b><font class="cushycms" color="blue"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Documento de Perfil da Empresa</font></font></font></font></font></b></a></em><br />
<br />
<strong><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">VISION :</font></font></font></font></strong><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">&nbsp;</font></font></font></font>Become The First Choice &amp; Preferred Partner for Asset (Equipment) Trade Financing &amp; Project Development in Mozambique.<br />
<br />
<strong><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">MISSION:&nbsp;</font></font></font></font></strong>Innovative &amp; out-of-the-box solutions that will allow our clients to easily doing business in Mozambique and in the region.<br />
<br />
<font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><b>VALUES:<kbd>&nbsp;</kbd></b></font></font></font></font>Integrity,&nbsp;Professionalism,&nbsp;Creativity, Quality, Transparency, Sustainability<em>.</em></p>

<h4 class="cushycms" style="text-align: justify;"><strong><span style="font-size:16px;"><b style="font-family: Carlito, sans-serif; font-size: 16px; text-align: justify;"><span style="font-family:&quot;Calibri&quot;,sans-serif"><span style="color:#948a54"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;"><font style="vertical-align: inherit;">Our Philosophy</font></font></font></font></span></span></b></span></strong></h4>

<p class="cushycms" style="text-align: justify;">Our main objective is to provide our partner (customer) with a unique experience of our services. For this purpose, together with our international and national partners, we seek to serve your company with the best experience and with highly competitive prices in the Procurement and Financing area, providing equipment, mainly to established companies. It should be noted that we perform our services with the highest international standards of corporate governance.</p>

<h4 class="cushycms" style="text-align: justify;"><font color="#948a54" face="Calibri, sans-serif"><span style="font-size: 21.3333px;">Clients and Partnership&nbsp;</span></font></h4>

<p class="cushycms" style="text-align: justify;">FEDHA is servicing Assets (equipment) and Financing to reputable, and credible companies that<font style="vertical-align: inherit;"><font style="vertical-align: inherit;">:</font></font><br />
<font style="vertical-align: inherit;"><font style="vertical-align: inherit;">&bull; </font></font>Have Strong Balance Sheets<font style="vertical-align: inherit;"><font style="vertical-align: inherit;">. </font></font><br />
<font style="vertical-align: inherit;"><font style="vertical-align: inherit;">&bull;&nbsp;</font></font>Track record and experience that have been operationally for many years.<br />
<font style="vertical-align: inherit;"><font style="vertical-align: inherit;">&bull;&nbsp;</font></font>Have gone through a bad cycle or recession and survived (experience and resilience).<br />
<font style="vertical-align: inherit;"><font style="vertical-align: inherit;">&bull;&nbsp;</font></font>Have Strong &amp; Firm Off-take Agreements or Contracts, that allow them to acquire and pay for the Assets (equipment) mitigating possible Risks.<br />
<font style="vertical-align: inherit;"><font style="vertical-align: inherit;">&bull;&nbsp;</font></font>Have ability to borrow in $US Dollar Loans and repayment ability in $US Dollars. Local currency Metical is also used when necessary.</p>
</div>

            </div>
         </div>

      </div>  
      <div>
<!--
<section class="business-talking">
	<div class="container">
        <h2>Our Team</h2>
    </div>
</section>

         <div class="container">
            <div class="row team">
               <div class="col-md-4 b1">
                  <img class="img-responsive" src="images/picTeam/1.png">
                  <h4>Mateus Katupha</h4>
                  <h5 class="cushycms">Chairman &amp; President (Non-Executive)</h5>
				  <ul>
                     <li><a target="_blank" href="https://www.linkedin.com/in/jkatupha"><img src="images/share2.png"/></a></li>
                  </ul>
					<p class="cushycms" style="text-align:justify"><b>Expertise</b>: Planning &amp; Strategic Risk Management<br />
<b>Employment record</b><br />
Chairman of the Board of Directors of Petr&oacute;leos de Mo&ccedil;ambique - Petromoc<br />
Deputy, member, and spokesman of the Standing Committee of the National Assembly<br />
Minister of Culture, Youth &amp; Sports<br />
<br />
<br />
<br />
&nbsp;</p>
               </div>
               <div class="col-md-4 b1">
                  <img class="img-responsive" src="images/picTeam/2.png">
                  <h4>Malengane Machel</h4>
                  <h5 class="cushycms">CEO</h5>
				  <ul>
                     <li><a target="_blank"><img src="images/share2.png"/></a></li>
                  </ul>
					<p class="cushycms" style="text-align:justify"><b>Expertise</b>: Strategy Development, Project structuring, and development<br />
<b>Employment record</b><br />
The Virgin Group<br />
Uninet Communication (Founder)<br />
Whatana Investments (Founder)<br />
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<br />
&nbsp;</p>
               </div>
               <div class="col-md-4 b3">
                  <img class="img-responsive" src="images/picTeam/3.png">
                  <h4>Rui Fonseca</h4>
                  <h5 class="cushycms">Non-Executive Director</h5>
				  <ul>
                     <li><a target="_blank"><img src="images/share2.png"/></a></li>
                  </ul>
					<p class="cushycms" style="text-align:justify"><b>Expertise</b>: Planning &amp; Strategic Risk Management<br />
<b>Employment record</b><br />
Chairman of BIM Bank<br />
Former Chairman of Vodacom Mozambique<br />
Former Chairman of Ports &amp; Railways of Mozambique, E.P. (CFM)<br />
Chairman of Beira Grain Terminal, Beira, Mozambique, PLC<br />
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<br />
&nbsp;</p>
               </div>
               <div class="col-md-4 b1">
                  <img class="img-responsive" src="images/picTeam/4.png">
                  <h4>Nuno Quelhas</h4>
                  <h5 class="cushycms">Executive Director</h5>
				  <ul>
                     <li><a target="_blank" href="https://www.linkedin.com/in/nuno-quelhas-752ab323"><img src="images/share2.png"/></a></li>
                  </ul>
					<p class="cushycms" style="text-align:justify"><b>Expertise</b>: Investment Banking; Strategy &amp; Management Consultancy<br />
<b>Employment record</b><br />
Deutsche Bank<br />
Deloitte Consulting<br />
Ernst &amp; Young<br />
Whatana Investments (Founder)<br />
<br />
<br />
<br />
<br />
<br />
&nbsp;</p>
               </div>
               <div class="col-md-4 b3">
                  <img class="img-responsive" src="images/picTeam/6.png">
                  <h4>João Gomes </h4>
                  <h5 class="cushycms">Executive Director</h5>
				  <ul>
                     <li><a target="_blank"href="https://www.linkedin.com/in/joão-gomes-5360581"><img src="images/share2.png"/></a></li>
                  </ul>
					<p class="cushycms" style="text-align:justify"><b>Expertise</b>: Strategic and Management Consulting<br />
<b>Employment record</b><br />
Ministry of Industry and Energy (Portugal)<br />
Ministry of Economy (Portugal)<br />
Arthur Andersen (Portugal)<br />
Deloitte (Portugal)<br />
Maksen (Mo&ccedil;ambique)<br />
<br />
<br />
<br />
<br />
<br />
<br />
&nbsp;</p>
               </div>
               <div class="col-md-4 b3">
                  <img class="img-responsive" src="images/picTeam/7.png">
                  <h4>Nuro Essimela</h4>
                     <h5 class="cushycms">Office Administrator and Personal Assistant</h5>
				  <ul>
                     <li><a target="_blank"href="https://www.linkedin.com/in/nuro-essimela-77389a116"><img src="images/share2.png"/></a></li>
                  </ul>
					<p class="cushycms" style="text-align:justify"><b>Expertise</b>:Personal Assistant &amp; Administrative support<br />
<b>Employment record</b><br />
Fedha Advisory S.A.<br />
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<br />
<br />
&nbsp;</p>
               </div>
            </div>
         </div>
			<div id="what-we-do"><br><br><br><br><br><br><br><br><br></div>
      </div>
END team
-->
      <!--action start-->    
      <div>
<section class="business-talking"><!--business-talking-start-->
	<div class="container">
        <h2>What we do</h2>
    </div>
</section>
         <div style="position: relative;">
            <div class="container">
<div class="row">
                  <div class="col-md-2 wwa"></div>
                  <div class="col-md-8 project">
                     <span name="about"></span>
                     <h2 style="text-align:justify">Business Development</h2>
                     <p style="text-align:justify">
                        Fedha Advisory S.A. use its best efforts and profession expertise of our senior advisors and project developers to create and maintain value for our custumers through:
                     </p>
                     <ul>
                        <li>
                           <p class="cushycms" style="text-align:justify">Competitive Pricing of Assets (Equipment) and Financing (both local and international interest rates %.</p>
                        </li>    
                        
                        <li>
                           <p class="cushycms" style="text-align:justify">Acquisition, Supply and Supply of Assets (Equipment), from Point to Point</p>
                        </li>
                        <li>
                            <p class="cushycms" style="text-align:justify">Guarantee valeu add in the promotion and supply of quality equipment for Businesses, Projects or Services to specific customers who need equipment as a central component of their operations.</p>
                        </li>
                        <li>
                           <p class="cushycms" style="text-align:justify">Funding and Financing, with credit guarantee insurance from local and/or international institutions.</p></li><li>
                           <p style="text-align:justify">Negotiating all the terms and conditions for the development of the projects
                        </p></li><li>
                           <p class="cushycms" style="text-align:justify">Participate in Mozambique&rsquo;s &ldquo;Local Content&rdquo; if and when necessary.</p></li>
                     <br>
                     
                     <h2 class="cushycms" style="text-align:justify"><font color="#948a54"><font face="Calibri, sans-serif"><span style="font-size:21.3333px"><b>Value Creation&nbsp;</b></span></font></font></h2>
                     
					</li></ul>
						<p class="cushycms" style="text-align:justify"><p style="text-align: justify;">Fedha&#39;s Unique Value Proposition:<br />
<br />
Currently the only company in Mozambique with the combined Procurement (P) &amp; Financing of Assets/Equipment to ensure the clients, partners and projects get:</p>
</p><ul>
                           <li>
                              <p class="cushycms" style="text-align:justify">Quality equipment at competitive pricing;</p>
                           </li><li>
                              <p class="cushycms" style="text-align:justify">Point to Point Delivery</p>
                           </li><li>
                              <p class="cushycms" style="text-align:justify">Funds &amp; Funding for companies to acquire the necessary assets (equipment) for their business</p>
                           </li><li>
                              <p class="cushycms" style="text-align:justify"><p style="text-align: justify;">Expert international partners with over 30 Years track record to minimize risk and timely delivery</p>
</p>
                           </li><li>
                              <p class="cushycms" style="text-align:justify">Reduce bureaucracy and the speed of doing business</p>
                        </li></ul>
                        

					</li></ul>
						<p class="cushycms" style="text-align:justify">Ability to adapt to changes</p><ul>
                           <li>
                              <p class="cushycms" style="text-align:justify">Proactivity</p>
                           </li><li>
                              <p class="cushycms" style="text-align:justify">A highly motivated team with excellent professionals&nbsp;</p>
                           </li><li>
                              <p class="cushycms" style="text-align:justify"><span style="font-size: 16px;">Sophisticated services</span></p>
                           </li><li>
                              <p class="cushycms" style="text-align:justify">Efficient communication</p>
                           </li><li>
                              <p class="cushycms" style="text-align:justify">Diverse options for your needs</p>
                        </li></ul>
                     
                     
                  </div>
                  <div class="col-md-2 wwa"></div>
               </div>
            </div>
         </div>
      </div>
      <!--END action-->
	  
	  
	  
	  
	<!--Projects start-->    
      <div>
<section class="business-talking"><!--business-talking-start-->
	<div class="container">
        <h2>Our Portfolio</h2>
    </div>
</section>
         <div style="position: relative;">
            <div class="container">
               <div class="row">
                  <div class="col-md-2 wwa"></div>
                  <div class="col-md-8 project">
                   
					<h2 style="text-align:justify">Projects</h2>
					<p class="cushycms" style="text-align:justify"></p>
                    
					<h2 class="cushycms" style="text-align:justify"><font color="#948a54" face="Calibri, sans-serif"><span style="font-size: 21.3333px;"><b>SOME CLIENTS SERVED by FEDHA</b></span></font></h2>
                     <p class="cushycms" style="text-align:left"><h1><img alt="" src="https://lh3.googleusercontent.com/nudq0M13FTrSmRk7kkXQO_6tyDCfFVzGoJTJzu3CnE67qv5f8-D1FbNpLeskEZC3gz0mtVuBGybi5tJEIL3qeqUIPiBhNFsiXL172iU9iOqPILYEiF8DxSNF-BGdoZsDSPX4PThwpcKFMZ5J-jAvZeohZDf2Hn7pAWzTq6ChxlBnc__JbidLc7ubXR_N6aXBGzZh2RZns7VFmhF8XkZQfjwVh_O4lhIR6Ppp2c0xV-4kUoS-PHs_4KhQo12pwXeVHEPva99As1T4c-8QbGJelD4tUPHprauJnR5sou1W_gMxDKXhjRBr5pFqIlOizdA6XVFZkBlaRt-L4CHoFJtAbH1TP2kdaVsL-qIiMBTypazqOYWjj-JgpGkknDHe2lPV9FQNroJaS7j9kPFG--GhehcUq3fEFDtYhtRhIFr6uXpW5Kk6ESTtehTk8579homz8bqonh7vlEjyt77oQclGmz0YJ0WkJ8kCQTO7T9M_8o4YPiHQvS1nY6jAT_RRjGfgvRYTVDdsSAq8olqgzHrLsGUXd9CaozCW_9AqUbhZalGns9FI_VaGF9nCurPqfOVO3nwoT1s5QKCBTzDZq3EYfvNI8dbNDP0fsL13jnJKDk5g0DhTicwVxR4dmZJiHrEmchOfImbBtTQZ1CiAlljmvgIueqXgUmwZNkUA3Y2icQPbNNyMldrSUmlGYlX89Z9jyfrqi-NeBwycSsRNx8VE2Y1i=w1366-h513-no?authuser=0" style="width: 975px; height: 365px;" /></h1>
</div>
               </div>
            </div>
         </div>
      </div>
      <!--END Projects-->
	  
	  
	  

               </div>
            </div>
         </div>

	  
	  
      <!--END partners-->
      <!--col-md-2 wwa"></div>
                  <div class="col-md-8 project">
                   
					<h2 style="text-align:justify">Our Clients</h2>

                  </div>
                  <div class="col-md-2 wwa"></div>
               </div>
            </div>
         </div>
	  
      <div class="container">
      <div class="row1">
      <div class="col-md-12">


      

      

      

      
      <div class="col-md-2 bar">
      <a href="http://www.edm.co.mz/" target="blank"><img class="img-responsive" src="images/client/6.png"></a>
      </div>
      
    <div class="col-md-3 bar">
      <a href="http://www.petromoc.co.mz/" target="blank"><img class="img-responsive" src="images/client/4.png"></a>
      </div>
      
      <div class="col-md-2 bar">
      <a href="http://www.eoh.co.za/" target="blank"><img class="img-responsive" src="images/client/3.png"></a>
      </div>
      
      <div class="col-md-3 bar">
      <a href="http://www.avic.com/en" target="blank"><img class="img-responsive" src="images/client/2.jpg"></a>
      </div>
      
      <div class="col-md-2 bar">
      <a href="http://svrweb.cabelte.pt/en-us/" target="blank"><img class="img-responsive" src="images/client/1.png"></a>
      </div>   
      

      

      
      </div>			
      </div>
      </div>
	  </div>

	  <div style="position: relative;">
            <div class="container">
               <div class="row">
                  <div class="col-md-2 wwa"></div>
                  <div class="col-md-8 project">
                   
					<h2 class="cushycms" style="text-align:justify">Our Partners</h2>
					<p class="cushycms" style="text-align:justify; color:#666666">Our partners range from International &amp; National Companies, for:
<ul>
	<li>Equipment</li>
	<li>Procurement</li>
	<li>Supply</li>
	<li>Logistics and point-point delivery</li>
	<li>Financial Insitutions</li>
	<li>Insurance Institutions and Solutions</li>
</ul>
</p>
						
                  </div>
                  <div class="col-md-2 wwa"></div>
               </div>
            </div>
			<div id="contacts"><br><br><br><br><br><br><br><br><br></div>
         </div>

	  
	  
      <!--END partners-->
      <!--about contact-->    
      <div >
<section class="business-talking"><!--business-talking-start-->
	<div class="container">
        <h2>Contact Us</h2>
    </div>
</section>
      <div class="container">
  
		<div class="row">
<div class="col-md-6 col-xs-12 cont">
				<iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d1268.3071874614727!2d32.60231077398522!3d-25.957725610137405!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1ee69bb4f229d7fd%3A0x75eac4cabf10c905!2sRua+Orlando+Mendes%2C+Maputo!5e0!3m2!1sen!2smz!4v1506849995191" width="100%" height="40%" frameborder="0" style="border:0" allowfullscreen=""></iframe>
					<font size="3px"><img src="images/icon_share.png" title="Share on Social Media">
					<a target="_blank" href="https://www.google.com/maps/place/Rua+Orlando+Mendes,+Maputo,+Mozambique/@-25.958126,32.601411,16z/data=!4m5!3m4!1s0x1ee69bb4f229d7fd:0x75eac4cabf10c905!8m2!3d-25.9581256!4d32.6014109?hl=en-US"><img src="images/maps.png" title="Share location using google maps"></a>
					<a class="fb-xfbml-parse-ignore" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fevolutiounlimited.com%2Ffedha&amp;src=sdkpreparse"><img src="images/share.png" title="Share on Facebook"></a>
					<a href="http://www.linkedin.com/shareArticle?mini=true&amp;url=https://evolutiounlimited.com/fedha" target="_blank"><img src="images/share2.png" alt="LinkedIn" title="Share on LinkedIn"></a>
					
					
					<br><br>
					<i class="fa fa-home"></i><font size="3px">Fedha Advisory S.A<br>
					<i class="fa fa-home"></i><font size="3px">Rua Orlando Mendes, nº 148 Sommerschield Maputo, Moçambique<br>
					<i class="fa fa-phone"></i><font size="3px">+258 21 497570<br>
					<font size="3px">Contact Person: <b>Nuro Essimela</b><br>
					<a href="mailto:nessimela@fedhadvisory.com"><i class="fa fa-envelope"></i><font size="3px">nessimela@fedhadvisory.com<br></font></a><font size="3px">
					<br><br>
					
				
			</font></font></font></font></font></font></div>
				  
			  <div class="col-md-6 col-xs-12 forma">
   <?php
//    include('Mail.php');
if ($_POST["email"] <> '') {
    $ToEmail      = 'nessimela@fedhadvisory.com';
    $EmailSubject = 'Fedha Advisory website contact';
    $mailheader   = "From: " . $_POST["email"] . "\r\n";
    $mailheader .= "Reply-To: " . $_POST["email"] . "\r\n";
    $mailheader .= "Content-type: text/html; charset=iso-8859-1\r\n";
    $MESSAGE_BODY = "Name: " . $_POST["name"] . "";
    $MESSAGE_BODY .= "Email: " . $_POST["email"] . "";
    $MESSAGE_BODY .= "Comment: " . nl2br($_POST["comment"]) . "";
    mail('mucapera@gmail.com', $EmailSubject, $MESSAGE_BODY, $mailheader);
    
    
    mail($ToEmail, $EmailSubject, $MESSAGE_BODY, $mailheader) or die("Oops! An error occurred while trying to send your email.<br>Please send an email to <u>nessimela@fedhadvisory.com</u>, or try again later.<br>We appologise for the inconvience.");
?> 
Your message has been sent!
<?php
} else {
?> 
        <form action="index.php" method="post"> 
        <div class="form">
            <input class="col-md-6 col-xs-12 name" name="name" onblur="if (this.value == '')  this.value = this.defaultValue;" onfocus="if (this.value == this.defaultValue)this.value = '';" type="text" value="Your name *" /> 
            <input class="col-md-6 col-xs-12 Email" name='email' onblur="if (this.value == '')this.value = this.defaultValue;" onfocus="if (this.value == this.defaultValue)this.value = '';" type="text" value="Your E-mail *" />
            <textarea class="col-md-12 col-xs-12 Message" name="comment" cols="0" onblur="if (this.value == '') this.value = this.defaultValue;" onfocus="if (this.value == this.defaultValue) this.value = '';" rows="0">Your Message *</textarea> 
           	  <div class="cBtn col-xs-12" >
		  <ul>
		<input class="send" type="submit" value="Send Message"/>
		  </ul>
	  </div>

        </div>
    </form>
    <?php
}
;
?>
			  
			  </div>

		</div>
      </div>
	  
      <div class="linem">
		
      </div>
      <div class="lineBlack">
      <div class="container">
      <div class="row downLine">
      <div class="col-md-12 text-right">
      <!--input  id="searchPattern" type="search" name="pattern" value="Search the Site" onblur="if(this.value=='') {this.value='Search the Site'; }" onfocus="if(this.value =='Search the Site' ) this.value='';this.style.fontStyle='normal';" style="font-style: normal;"/-->
      </div>
      <div class="col-md-4 text-left copy">
      <p style="color:#a00404 ">© 2022 Fedha Advisory. All rights reserved. <br>Design & Development by <a style="color:#a00404 " href="http://www.evolutiounlimited.com"><b>EvolutioUnlimited</b></a></p>
      </div>
      <div class="col-md-8 text-right dm">
      <ul id="downMenu" >
		  <li><a href="#theFirm"style="color:#a00404 "><b>About Us</a></li>
		  <!--<li><a href="#team"style="color:#a00404 "><b>Our Team</a></li>-->
		  <li><a href="#what-we-do"style="color:#a00404 "><b>What we do</a></li>
			<li><a href="#portfolio"style="color:#a00404 "><b>Portfolio</a></li>
	  <li><a href="#clients-partners"style="color:#a00404 "><b>Clients & Partners</a></li>
	    <li class="last"><a href="#contacts"style="color:#a00404 "><b>Contacts</a></li>

      </ul>
      </div>
      </div>
      </div>
      </div>
      </div>
      <!--END contact-->
      <script src="js/jquery.prettyPhoto.js" type="text/javascript" charset="utf-8"></script>
      <script src="js/bootstrap.min.js"></script>
      <script src="js/jquery.slicknav.js"></script>
      <script>
         $(document).ready(function(){
         $(".bhide").click(function(){
         	$(".hideObj").slideDown();
         	$(this).hide(); //.attr()
         	return false;
         });
         $(".bhide2").click(function(){
         	$(".container.hideObj2").slideDown();
         	$(this).hide(); // .attr()
         	return false;
         });
         	
         $('.heart').mouseover(function(){
         		$(this).find('i').removeClass('fa-heart-o').addClass('fa-heart');
         	}).mouseout(function(){
         		$(this).find('i').removeClass('fa-heart').addClass('fa-heart-o');
         	});
         	
         	function sdf_FTS(_number,_decimal,_separator)
         	{
         	var decimal=(typeof(_decimal)!='undefined')?_decimal:2;
         	var separator=(typeof(_separator)!='undefined')?_separator:'';
         	var r=parseFloat(_number)
         	var exp10=Math.pow(10,decimal);
         	r=Math.round(r*exp10)/exp10;
         	rr=Number(r).toFixed(decimal).toString().split('.');
         	b=rr[0].replace(/(\d{1,3}(?=(\d{3})+(?:\.\d|\b)))/g,"\$1"+separator);
         	r=(rr[1]?b+'.'+rr[1]:b);
         
         	return r;
         }
         	
         setTimeout(function(){
         		$('#counter').text('0');
         		$('#counter1').text('0');
         		$('#counter2').text('0');
         		setInterval(function(){
         			
         			var curval=parseInt($('#counter').text());
         			var curval1=parseInt($('#counter1').text().replace(' ',''));
         			var curval2=parseInt($('#counter2').text());
         			if(curval<=707){
         				$('#counter').text(curval+1);
         			}
         			if(curval1<=12280){
         				$('#counter1').text(sdf_FTS((curval1+20),0,' '));
         			}
         			if(curval2<=245){
         				$('#counter2').text(curval2+1);
         			}
         		}, 2);
         		
         	}, 500);
         });
      </script>
      <script type="text/javascript">
         jQuery(document).ready(function(){
         	jQuery('#menu').slicknav();
         	
         });
      </script>
      <script type="text/javascript">
         $(document).ready(function(){
            
             var $menu = $("#menuF");
                 
             $(window).scroll(function(){
                 if ( $(this).scrollTop() > 100 && $menu.hasClass("default") ){
                     $menu.fadeOut('fast',function(){
                         $(this).removeClass("default")
                                .addClass("fixed transbg")
                                .fadeIn('fast');
                     });
                 } else if($(this).scrollTop() <= 100 && $menu.hasClass("fixed")) {
                     $menu.fadeOut('fast',function(){
                         $(this).removeClass("fixed transbg")
                                .addClass("default")
                                .fadeIn('fast');
                     });
                 }
             });
         });
         //jQuery
      </script>
      <script>
         /*menu*/
         function calculateScroll() {
         	var contentTop      =   [];
         	var contentBottom   =   [];
         	var winTop      =   $(window).scrollTop();
         	var rangeTop    =   200;
         	var rangeBottom =   500;
         	$('.navmenu').find('a').each(function(){
         		contentTop.push( $( $(this).attr('href') ).offset().top );
         		contentBottom.push( $( $(this).attr('href') ).offset().top + $( $(this).attr('href') ).height() );
         	})
         	$.each( contentTop, function(i){
         		if ( winTop > contentTop[i] - rangeTop && winTop < contentBottom[i] - rangeBottom ){
         			$('.navmenu li')
         			.removeClass('active')
         			.eq(i).addClass('active');				
         		}
         	})
         };
         
         $(document).ready(function(){
         	calculateScroll();
         	$(window).scroll(function(event) {
         		calculateScroll();
         	});
         	$('.navmenu ul li a').click(function() {  
         		$('html, body').animate({scrollTop: $(this.hash).offset().top - 80}, 800);
         		return false;
         	});
         });		
      </script>	
      <script type="text/javascript" charset="utf-8">
         jQuery(document).ready(function(){
         	jQuery(".pretty a[rel^='prettyPhoto']").prettyPhoto({animation_speed:'normal',theme:'light_square',slideshow:3000, autoplay_slideshow: true, social_tools: ''});
         	
         });
      </script>
   </body>
</html>