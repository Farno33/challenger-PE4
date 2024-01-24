<?php
array_map('unlink', array_filter((array) glob(DIR."sessions/*")));
?>
