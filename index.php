<?php
  // Check for form submit
  if (!isset($_POST['cmdEncode']) && !isset($_POST['cmdDecode'])) {
    // User has not yet submitted
    $_POST['chkBasics']= true;
  }
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
<title>Decoder - Encoder: UTF8, UTF16, ...</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link href="css/styles.css" rel="stylesheet" type="text/css" />

</head>

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-4635324-7']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

<body>
<div id="header">
  <div id="main">
    <h1><u>De</u>code, <u>En</u>code or <u>Ob</u>fuscate your string</h1>
    <p>This is used to obfuscate your string or code, to encode or decode a certain value. For more information on possible XSS/XSRF implementations, see <a href="http://ha.ckers.org/xss.html" target="_blank">http://ha.ckers.org/xss.html</a>.</p>
    <hr />
    <form method="post" action="index.php">
      <p>Enter your string in the textarea below. Am I missing a popular technique? <a href="mailto:m@ttias.be">Let me know!</a></p>
      <textarea name="txtCode" cols="80" rows="6"><?=isset($_POST['txtCode']) ? $_POST['txtCode'] : '' ?></textarea><br />
      <input type="checkbox" name="chkBasics" id="chkBasics" <?=isset($_POST['chkBasics']) ? 'checked' : '' ?> /> <label for="chkBasics">Include basic encoding/decoding (HTML, UTF-8, base64, URL encode, ...)</label><br />
      <input type="checkbox" name="chkOneWay" id="chkOneWay" <?=isset($_POST['chkOneWay']) ? 'checked' : '' ?> /> <label for="chkOneWay">Include one-way encryption (MD5, SHA1, RipeMD, Adler, Haval...)</label><br />
      <input type="checkbox" name="chkObfuscate" id="chkObfuscate" <?=isset($_POST['chkObfuscate']) ? 'checked' : '' ?> /> <label for="chkObfuscate">Include code obfuscation (Javascript, SQL, HTML)</label><br />
      <input type="submit" name="cmdEncode" value="Encode string" class="submit_button" /> <input type="submit" name="cmdDecode" value="Decode string" class="submit_button" />
    </form>

    <?php
      if (isset($_POST['cmdEncode']) && strlen($_POST['txtCode']) > 0) {
        // Encode this string
        $txtCode = $_POST['txtCode'];

        $arrCharCode = array();
        $arrCharCodeSQL = array();
        $arrCharCodeHexHtml = array();
        $arrCharCodeDecHtml = array();
        $arrCharCodeHexShortHtml = array();
        for ($i = 0; $i < strlen($txtCode); $i++) {
          $arrCharCode[]     = ord($txtCode[$i]);
          $arrCharCodeSQL[]   = "CHAR(". ord($txtCode[$i]) .")";
          $arrCharCodeHexHtml[]  = "&#x". dechex(ord($txtCode[$i]));
          $arrCharCodeDecHtml[]  = "&#". ord($txtCode[$i]);
          $arrCharCodeHexShortHtml[]  = "%". dechex(ord($txtCode[$i]));
        }

        echo "<br /><h1>Encoding results</h1>\n\n";

        if (isset($_POST['chkBasics'])) {
          echo "<h1>Basic encoding</h1>\n";
          // UTF-7
          echo "<h2>UTF-7 encode</h2>\n";
          echo "<xmp>". imap_utf7_encode($txtCode) ."</xmp>\n\n";

          // UTF-8
          echo "<h2>UTF-8 encode</h2>\n";
          echo "<xmp>". utf8_encode($txtCode) ."</xmp>\n\n";

          // UTF-16
          echo "<h2>UTF-16 encode</h2>\n";
          echo "<xmp>". mb_convert_encoding($txtCode, "UTF-16", "auto") ."</xmp>\n\n";

          // UTF-32
          echo "<h2>UTF-32 encode</h2>\n";
          echo "<xmp>". mb_convert_encoding($txtCode, "UTF-32", "auto") ."</xmp>\n\n";

          // rawurlencode
          echo "<h2>RAW URL encode</h2>\n";
          echo "<xmp>". rawurlencode($txtCode) ."</xmp>\n\n";

          // urlencode
          echo "<h2>URL encode simple</h2>\n";
          echo "<xmp>". urlencode($txtCode) ."</xmp>\n\n";

          // urlencode
          echo "<h2>URL encode full</h2>\n";
          echo "<xmp>". implode("", $arrCharCodeHexShortHtml) ."</xmp>\n\n";

          // HTML
          echo "<h2>HTML encode</h2>\n";
          echo "<xmp>". htmlentities($txtCode) ."</xmp>\n\n";

          // base64
          echo "<h2>Base64 encode</h2>\n";
          echo "<xmp>". base64_encode($txtCode) ."</xmp>\n\n";

          // uuencode
          echo "<h2>UUencode</h2>\n";
          echo "<xmp>". convert_uuencode($txtCode) ."</xmp>\n\n";
        }

        if (isset($_POST['chkOneWay'])) {
          echo "<h1>One way encoding</h1>\n";
          foreach (hash_algos() as $hash_algo) {
            echo "<h2>Hash: ". $hash_algo ."</h2>\n";
            echo "<xmp>". hash($hash_algo, $txtCode) ."</xmp>\n\n";
          }
        }

        if (isset($_POST['chkObfuscate'])) {
          echo "<h1>Obfuscation: JavaScript</h1>\n";
          // String.fromCharCode() in Javascript
          echo "<h2>fromCharCode()</h2>\n";
          echo "<xmp>document.write(String.fromCharCode(". implode(",", $arrCharCode) ."));</xmp>\n\n";

          // unescape() in Javascript
          echo "<h2>unescape()</h2>\n";
          echo "<xmp>document.write(unescape(\"". implode("", $arrCharCodeHexShortHtml) ."\"));</xmp>\n\n";

          echo "<h1>Obfuscation: SQL</h1>\n";
          // concat() char's
          echo "<h2>CONTACT of CHAR()'s</h2>\n";
          echo "<xmp>CONCAT(". implode(",", $arrCharCodeSQL) .")</xmp>\n\n";

          // char()
          echo "<h2>CHAR()</h2>\n";
          echo "<xmp>CHAR(". implode(",", $arrCharCode) .")</xmp>\n\n";

          echo "<h1>Obfuscation: HTML</h1>\n";
          // hexadecimal
          echo "<h2>HTML Hexadecimal with optional semicolons</h2>\n";
          echo "<xmp>". implode(";", $arrCharCodeHexHtml) .";</xmp>\n\n";

          // decimal
          echo "<h2>HTML Decimal with optional semicolons</h2>\n";
          echo "<xmp>". implode(";", $arrCharCodeDecHtml) .";</xmp>\n\n";
        }
      } elseif (isset($_POST['cmdDecode']) && strlen($_POST['txtCode']) > 0) {
        // Decode this string
        $txtCode = $_POST['txtCode'];

        if (isset($_POST['chkBasics'])) {
          echo "<h1>Basic encoding</h1>\n";
          // UTF-7
          echo "<h2>UTF-7 decoded</h2>\n";
          echo "<xmp>". imap_utf7_decode($txtCode) ."</xmp>\n\n";

          // UTF-8
          echo "<h2>UTF-8 decoded</h2>\n";
          echo "<xmp>". utf8_decode($txtCode) ."</xmp>\n\n";

          // UTF-16
          echo "<h2>UTF-16 decoded to UTF-8</h2>\n";
          echo "<xmp>". mb_convert_encoding($txtCode, "UTF-8", array("UTF-16")) ."</xmp>\n\n";

          // UTF-32
          echo "<h2>UTF-32 decoded to UTF-8</h2>\n";
          echo "<xmp>". mb_convert_encoding($txtCode, "UTF-8", array("UTF-16")) ."</xmp>\n\n";

          // rawurlencode
          echo "<h2>RAW URL decoded</h2>\n";
          echo "<xmp>". rawurldecode($txtCode) ."</xmp>\n\n";

          // urlencode
          echo "<h2>URL encode</h2>\n";
          echo "<xmp>". urlencode($txtCode) ."</xmp>\n\n";

          // HTML
          echo "<h2>HTML entities decoded</h2>\n";
          echo "<xmp>". html_entity_decode($txtCode) ."</xmp>\n\n";

          // base64
          echo "<h2>Base64 decoded</h2>\n";
          echo "<xmp>". base64_decode($txtCode) ."</xmp>\n\n";

          // uuencode
          echo "<h2>UUdecoded</h2>\n";
          echo "<xmp>". convert_uudecode($txtCode) ."</xmp>\n\n";
        }
      }
    ?>
  </div>

  <div id="footer">
    String decoder &amp; encoder | Created by <a href="http://mattiasgeniar.be" target="_blank">Mattias Geniar</a> | Source on <a href="https://github.com/mattiasgeniar/Encoder" target="_blank">Github</a>
  </div>
</div>
</body>
</html>
