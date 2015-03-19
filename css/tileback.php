<?php

header("Content-Type: text/css"); 
header("X-Content-Type-Options: nosniff");

$iBaseXDp = 0;
$iBaseYDp = 0;

$jsonBackground = file_get_contents("../background/Cityscape.json");

$jsonBackground = json_decode($jsonBackground, true);

$iTilesWide = $jsonBackground["tileswide"];
//$iWidthDp = $jsonBackground["tilewidth"];
$iWidthDp = 100 / $iTilesWide;

$iTilesHigh = $jsonBackground["tileshigh"];
//$iHeightDp = $jsonBackground["tileheight"];
$iHeightDp = 100 / $iTilesHigh;

// I guess map tiles to filenames
$aTiles = array();

$aLayer = $jsonBackground["layers"][0]["tiles"];
foreach ($aLayer as $iTile => $aTile)
{
	$aTileCss = array();
	$aTileCss["position-x"] = $aTile["x"] * $iWidthDp + $iBaseXDp;
	$aTileCss["position-y"] = $aTile["y"] * $iHeightDp + $iBaseYDp;
	$aTileCss["size-x"] = $iWidthDp;
	$aTileCss["size-y"] = $iHeightDp;

	$iTileType = $aTile["tile"];
	$strTileURL = 
		"/background/Cityscape_" .
		sprintf("%'.02d", $iTileType) . 
		".png";
	$strTileURL = "url(" . $strTileURL . ")";

	$aTileCss["url"] = $strTileURL;

	// Repeat the extreme tile per row.
	if ($aTile["x"] == 0)
	{
		$aTileCss["repeat"] = "repeat-x";
	}
	else
	{
		$aTileCss["repeat"] = "no-repeat";
	}

	$aTiles[$iTile] = $aTileCss;
}

$aTiles = array_reverse($aTiles);
$aBackgroundTile = array_shift($aTiles);
// Do this to force repeating the rest of the way. Looks ugly sometimes
//$aBackgroundTile["repeat"] = "repeat";
array_push($aTiles, $aBackgroundTile);

$aCss = array();
$aCss["urls"] = "";
$aCss["positions"] = "";
$aCss["sizes"] = "";
$aCss["position-x"] = "";
$aCss["size-x"] = "";
$aCss["position-y"] = "";
$aCss["size-y"] = "";

$aCss["repeats"] = "";

$i = 0;
$strUnit = "vmin";
foreach ($aTiles as $aTile) 
{
	$aCss["urls"] .= $aTile["url"] . ", ";
	$aCss["positions"] .= 
		$aTile["position-x"] . "$strUnit " . 
		$aTile["position-y"] . "$strUnit, ";
	$aCss["sizes"] .= 
		$aTile["size-x"] . "$strUnit " . 
		$aTile["size-y"] . "$strUnit, ";

	$aCss["position-x"] .= 
		$aTile["position-x"] . "$strUnit, "; 
	$aCss["position-y"] .= 
		$aTile["position-y"] . "$strUnit, ";
	$aCss["size-x"] .= 
		$aTile["size-x"] . "$strUnit, "; 
	$aCss["size-y"] .= 
		$aTile["size-y"] . "$strUnit, ";

	$aCss["repeats"] .= $aTile["repeat"] . ", ";
}

foreach ($aCss as &$strProperty)
{
	$strProperty = rtrim($strProperty, ", ");

	unset($strProperty);
}

$strUrls = $aCss["urls"];
$strPositions = $aCss["positions"];
$strSizes = $aCss["sizes"];

$strPositionX = $aCss["position-x"];
$strSizeX = $aCss["size-x"];
$strPositionY = $aCss["position-y"];
$strSizeY = $aCss["size-y"];

$strRepeats = $aCss["repeats"];


$strHeight = $iTilesHigh * $iHeightDp . "$strUnit";

echo <<<CSS
.footer {
		position: absolute;
		bottom: 0;
		width: 100%;
	    /* Set the fixed height of the footer here */
		height: 65vmin;
		/*background-color: #f5f5f5;*/
		background: 
			$strUrls;
		background-position-x: 
			$strPositionX;
		background-position-y: 
			$strPositionY;

		background-size: 
			$strSizes;

		background-repeat: 
			$strRepeats;
}
CSS;
