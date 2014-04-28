<?php

//load bootstrap
require ("../application/bootstrap.php");

/*
//profile
require("/usr/share/xhprof/xhprof_lib/utils/xhprof_lib.php");
require("/usr/share/xhprof/xhprof_lib/utils/xhprof_runs.php");

xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
*/

//start application
$application = Feather\Mvc\Application(realpath(dirname(__FILE__))."/../");
$application->init()->run();

/*
$xhprof_data = xhprof_disable();
$xhprof_runs = new XHProfRuns_Default();
$run_id = $xhprof_runs->save_run($xhprof_data, "xhprof_lightmvc");
*/
