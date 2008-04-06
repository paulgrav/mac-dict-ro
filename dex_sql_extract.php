<?php





echo <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<!--
        This is a sample dictionary source file.
        It can be built using Dictionary Development Kit.
-->
<d:dictionary xmlns="http://www.w3.org/1999/xhtml" xmlns:d="http://www.apple.com/DTDs/DictionaryService-1.0.rng">
<d:entry id="dictionary_application" d:title="Dictionary application">
        <d:index d:value="Dictionary application"/>
        <h1>Dicționarul Explicatic al Limbii Române</h1>
        <p>
                An application to look up dictionary on Mac OS X.<br/>
        </p>
        <span class="column">
                The Dictionary application first appeared in Tiger.
        </span>
        <span class="picture">
                It's application icon looks like below.<br/>
                <img src="Images/dictionary.png" alt="Dictionary.app Icon"/>
        </span>
</d:entry>
EOD;













$link = mysql_connect('demeter', 'root')
    or die('Could not connect: ' . mysql_error());

mysql_select_db('dex') or die('Could not select database');
mysql_query("SET NAMES 'utf8'");
// Performing SQL query






//  LOOP Definitions

	// GET words





$search = array('ţ','ş','Ţ','Ş');
$replace = array('ț','ș','Ț','Ș');

$lexem_query = "SELECT lexem_id, lexem_utf8_general FROM lexems ORDER BY lexem_utf8_general";
$lexems = mysql_query($lexem_query);
$prev_lexem_word= "";

while ($line = mysql_fetch_array($lexems, MYSQL_ASSOC)) {
	
	$lexem_word = $line['lexem_utf8_general'];
	$lexem_id = $line['lexem_id'];
	
	$query = "SELECT wl_utf8_general from wordlist WHERE wl_lexem = $lexem_id ORDER BY wl_analyse;";
	
	$words = mysql_query($query);
	$word_list = "";
	while($row = mysql_fetch_array($words, MYSQL_ASSOC)) {		
		$word_list .= '<d:index d:value="'. $row['wl_utf8_general']  .'"/>' . "\n";
	}
	
	$query = "SELECT HtmlRep FROM LexemDefinitionMap LEFT OUTER JOIN Definition ON (LexemDefinitionMap.DefinitionId = Definition.Id) WHERE LexemId = $lexem_id AND SourceId = 9";
	$definitions = mysql_query($query);
	$definition_list = "";
	while($row = mysql_fetch_array($definitions, MYSQL_ASSOC)) {
		$definition_list .= "<li>" . $row['HtmlRep'] . "</li>\n";
	}
	
	
	if(mysql_num_rows($words) == 0 OR mysql_num_rows($definitions) == 0) {
		continue;
	}

	$output = <<<EOD
<d:entry id="${lexem_word}-${lexem_id}" d:title="$lexem_word">
	$word_list
	<div d:priority="2"><h1>$lexem_word</h1></div>
	<div>
		<ol>
			$definition_list
		</ol>
	</div>
</d:entry>

EOD;

	echo str_replace($search,$replace,$output);
	//echo $output;
	mysql_free_result($definitions);
	mysql_free_result($words);
	$prev_lexem_word = $lexem_word;
}




// Free resultset
mysql_free_result($lexems);

// Closing connection
mysql_close($link);

echo <<<EOD
<d:entry id="front_back_matter" d:title="Front/Back Matter">
        <h1><b>My Dictionary</b></h1>
        <h2>Front/Back Matter</h2>
        <div>
                This is a front matter page of the sample dictionary.<br/><br/>
        </div>
        <div>
                <b>To see</b> this page,
                <ol>
                        <li>Open "Go" menu.</li>
                        <li>Choose "Front/Back Matter" menu item.
                        If it has sub-menu items, choose one of them.</li>
                </ol>
        </div>
        <div>
                <b>To prepare</b> the menu item, do the followings.
                <ol>
                        <li>Prepare this page source as an entry.</li>
                        <li>Add "DCSDictionaryFrontMatterReferenceID" key and its value to the plist of the dictionary.
                        The value should be the string of this page entry id. </li>
                </ol>
        </div>
        <br/>
</d:entry>
</d:dictionary>
EOD;

?>
