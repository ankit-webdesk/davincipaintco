<?php 

if (function_exists("set_time_limit") == TRUE AND @ini_get("safe_mode") == 0)
{
    @set_time_limit(3000000);// change according to your requirement
}

?>