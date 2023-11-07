<?php

class dissociatedpress {

function dissociate ($str, $randomstart = true, $groupsize = 4, $max = 128) {
	if ($groupsize < 2) {
		$groupsize = 2;
	}
		// Capitalize the first word
	$capital = true;

		//Remove from corpus, they just make the result confusing
	$str = str_replace(array("(",")","[","]","{","}"), array(),$str);

		//Break up tokens
	$tokens = preg_split("/[ \r\n\t]/",$str);
	
		//Clean up token array
	for ($i = 0; $i < sizeof($tokens); $i++){
		if ($tokens[$i] == ""){
			unset($tokens[$i]);
		}
	}

	$tokens = array_values($tokens);

		//Init variables
	$return = "";
	$lastmatch = array();

		// if we start at the beginning, start there
	if (!$randomstart) {
		for ($n = 0; $n < $groupsize; $n++){
			array_push($lastmatch,$tokens[$n]);
			$res = cleanToken($tokens[$n],$capital);
			$return .= $res[0];
			$capital = $res[1];
		}
	}

		//Loop until we have enough output
	$i = 0;
	while ($i < $max + 32){
			// Try and end on a full sentence
		if ($i > $max - 8 and $capital){
			break;
		}

			//If the lastmatch group isn't good enough, start randomly
		if (sizeof($lastmatch) < $groupsize){
			$loc = rand(0,sizeof($tokens)-$groupsize);
			$lastmatch = array();
			for ($n = 0; $n < $groupsize; $n++){
				array_push($lastmatch,$tokens[$loc+$n]);
				$res = dissociatedpress::cleanToken($tokens[$loc+$n],$capital);
				$return .= $res[0];
				$capital = $res[1];
			}
		} else {
			$chains = dissociatedpress::findChains($tokens, $lastmatch);
			$lastmatch = array();

				// If there aren't enough chains, start randomly next time (avoid getting caught in loops)
			if (sizeof($chains) > 2) {
				$loc = $chains[rand(0, sizeof($chains)-1)];
				for ($n = 0; $n < $groupsize; $n++){
					array_push($lastmatch,$tokens[$loc+$n]);
						$res = dissociatedpress::cleanToken($tokens[$loc+$n],$capital);
					$return .= $res[0];
					$capital = $res[1];
				}
			}
		}
		$i++;
	}
	
	return $return;
}

/**
 * Join the tokens with proper typography
 */

function cleanToken($token,$capital) {
	if ($capital){
		$token = ucfirst($token);
		$capital = false;
	}

	if (substr($token,-1,1) == '.'){
		$capital = true;
		return array($token . "  ",$capital);
	} else {
		return array($token . " ",$capital);
	}
}

/**
 * Naively find possible Markov Chains
 */

function findChains($haystack, $needle) {
	$return = array();
	for ($i = 0; $i < sizeof($haystack) - sizeof($needle); $i++){
		if ($haystack[$i] == $needle[0]){
			$matches = true;
			for ($j = 1; $j < sizeof($needle); $j++){
				if ($haystack[$i+$j] != $needle[$j]){
					$matches = false;
					break;
				}
			}
			if ($matches == true){
				array_push($return,$i+sizeof($needle));
			}
		}
	}
	return $return;
}

}
?>
