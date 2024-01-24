<?php

/* *********************************************************/
/* Challenger V3 : Gestion de l'organisation du Challenge **/
/* Créé par Raphaël Kichot' MOULIN *************************/
/* raphael.moulin@ecl13.ec-lyon.fr *************************/
/* *********************************************************/
/* templates/_footer.php ***********************************/
/* Bas de page *********************************************/
/* *********************************************************/
/* Dernière modification : le 18/02/15 *********************/
/* *********************************************************/

?>

			</div>
			
			<footer class="noprint" style="position:fixed">
				<span class='ver'>©Challenger <span class="ver-small">V3.6</span> <span class='ver-large'><?php
				$branch = trim(substr(file_get_contents(DIR.'.git/HEAD'), 16));
				echo $branch
				     .'_'.
					 substr(trim(file_get_contents(DIR.'.git/refs/heads/'.$branch)),0,8); // => renvoie le hash du commit (version courte)
				?></span> 
				</span>
				- <a href="<?php url('contact'); ?>">Contact</a>
				- <a href="<?php url('reglement'); ?>">Règlement</a> 
				- <a href="<?php url('classement'); ?>">Classement</a>
			</footer>
		</div>

		<script type="text/javascript">
		$(function() {
			$('.nojs').css('display', 'block');
			
			<?php if (!empty($_SESSION['user']) &&
				empty($_SESSION['user']['cas']) &&
				empty($_SESSION['user']['remember'])) { ?>

			setInterval(function() { 
				$.ajax({
					url: '<?php url('check'); ?>',
					success: function(data) {
						if (data != '1')
							window.location.href = "<?php url('login'); ?>";
					}
				}); }, <?php echo round(1000 * APP_SESSION_MAX_TIME / 10); ?>);

			<?php 

			} if (!empty($_SESSION['user']['geoip']) &&
				$_SESSION['user']['geoip'] > 0) { 
				$_SESSION['user']['geoip'] *= -1;
			
			?>

			$.ajax('<?php url('geoip'); ?>');

			<?php } ?>
			
		});
		</script>
	</body>
</html>