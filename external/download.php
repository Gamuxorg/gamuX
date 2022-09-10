<?php
	define("LBKEY", 0x74c9e101);		// random key 
	define("MIN_MEMSIZE", 80);			// php complain about memory size too small if less that 80
	define("ID", 1);

	$server_list = [
		"de.linuxgame.cn",
		"bg.linuxgame.cn"
	];
	$size = count($server_list);
	$filepath = ($_GET["filepath"]);
	
	/* simple round-robin algorithm, 
	   we store the load_blancing index with LBKEY using shared memory */
	$mem = shm_attach(LBKEY, MIN_MEMSIZE, 0600);
	if(!$mem) {
		echo "Server error : Out of memory!";
		exit(1);
	}

	// init 
	if(!shm_has_var($mem, ID))
		shm_put_var($mem, ID, 0);

	$index = shm_get_var($mem, ID) % $size;
	$url = "https://{$server_list[$index]}/$filepath";
	shm_put_var($mem, ID, ($index+1));
	shm_detach($mem);

	// redirect
	header("Location: " . $url, true, 301);
