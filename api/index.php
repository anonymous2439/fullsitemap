<html>
	<head>
		<style>
			.wrapper{
				width:1375px;
				margin:auto;
			}
			.results{
				text-align:center;
			}
			#banners{
				display:flex;
				flex-wrap:wrap;
			}
			#banners section{
				margin:10px;
				border: 1px solid #c4c4c4;
				padding:5px;
				width:30%;
				text-align:center;
			}
			#banners section img {
				width: 310px;
				height: auto;
				display:block;
				margin:auto;
			}
			#banners section a{
				display:inline-block;
				width:310px;
				min-height:36px;
				padding:5px;
			}
			.duplicate{
				background:red;
			}
			form{
				text-align:center;
			}
			form input{
				width:300px;
			}
			
		</style>
	</head>
	<body>
		<div class="wrapper">
			<form method="get">
				<input type="text" name="search_data">
				<button type="submit" name="search_submit">Search non-home banner images</button>
			</form>
		</div>
	</body>
</html>
<?php
	if(isset($_GET['search_submit'])){
		function get_inner_html( $node ) 
		{
			$innerHTML= '';
			$children = $node->childNodes;
			
			foreach ($children as $child)
			{
				$innerHTML .= $child->ownerDocument->saveXML( $child );
			}
			
			return $innerHTML;
		}

		//getting links from sitemap
		$dom = new DOMDocument;
		libxml_use_internal_errors(true);
		$dom->loadHTML('...');
		libxml_clear_errors();
		$dom = new DOMDocument;
		$dom->loadHtmlFile(''.str_replace('https','http',$_GET['search_data']).'/sitemap');
		$xpath = new DOMXPath($dom);

		$elements = $xpath->query('//div[@class="entry-content"]/ul/li/a');
		$elements2 = $xpath->query('//div[@class="entry-content"]/ul/li/ul/li/a');
		echo '<p class="results">'.($elements->length+$elements2->length-1).' results found!</p>';
		$links = array();
		if ($elements->length) {
			foreach ($elements as $element)
			{
				array_push($links, str_replace('https','http',$element->getAttribute('href')));
			}
		} else {
			echo "not found";
		}
		if ($elements2->length) {
			foreach ($elements2 as $element)
			{
				array_push($links, str_replace('https','http',$element->getAttribute('href')));
			}
		} else {
			echo "not found";
		}
		//end

		//getting images from gathered links
		echo '<div class="wrapper"><div id="banners">';
		$link_count = 0;
		foreach($links as $link){
			if($link != str_replace('https','http',$_GET['search_data']) && $link != str_replace('https','http',$_GET['search_data'].'/') && $link != str_replace('https','http',$_GET['search_data'].'/home')){
				$dom->loadHtmlFile($link);
				$xpath = new DOMXPath($dom);
				$elements = $xpath->query('//*[@class="non_ban_img"]/img');
				$images = array();
				preg_match("/[^\/]+$/", $elements->item(0)->getAttribute('src'), $matches);
				$image_filename = $matches[0];
				array_push($images, $image_filename);

				/*$link_count2 = 0;
				foreach($links as $link2){
					$dom->loadHtmlFile($link2);
					$xpath = new DOMXPath($dom);
					$elements = $xpath->query('//*[@class="non_ban_img"]/img');
					preg_match("/[^\/]+$/", $elements->item(0)->getAttribute('src'), $matches2);
					$image_filename2 = $matches2[0];
					if($image_filename == $image_filename2 && $link_count2 != $link_count ){
						echo '<section class="duplicate"><a href="'.$link.'">'.$link.'-'.$image_filename.'-'.$image_filename2.'</a>';
						break;
					}
					else{
						$link_count2++;
						continue;
					}
					echo '<section><a href="'.$link.'">'.$link.'</a>';
					$link_count2++;
				}*/
				
				
				echo '<section><a href="'.$link.'">'.$link.'</a>';
				echo $dom->saveHTML($elements->item(0));
				echo '</section>';
			}
			$link_count++;
		}
		echo '</div></div>';
		//end
	}
?>
