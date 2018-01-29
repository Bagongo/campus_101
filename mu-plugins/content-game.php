<?php

function university_query_vars($vars)
{
	$vars[] = "pageKeyWord";

	return $vars;
}

add_filter("query_vars", "university_query_vars");

function returnPageKeyWord($content)
{
	$wordsInContent = explode(" ", $content);

	if(count($wordsInContent) > 2)
	{
		$keyWord = $wordsInContent[2];
		$keyWord = preg_replace("#((?!')\pP)+#", '', $keyWord);

		return $keyWord;
	}

	return NULL;
}