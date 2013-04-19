<div class="IpTweets">
	<?php if(is_array($tweets) && count($tweets) > 0): ?>
		<ul>
			<?php foreach($tweets as  $tweet): ?>
				<li> 
					<?php echo $tweet->text; ?>
					<a href="<?php echo $tweet->tweet_url; ?>" target="_blank" class="time"><?php echo $tweet->time_ago; ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else: ?>
		
		<?php if($site->managementState()): ?>
			<h2>Can't access user "<?php echo $username; ?>". Please check if user exists.</h2>
		<?php else: ?>
			<h2>Twitter is temporarily down.</h2>
		<?php endif; ?>

	<?php endif; ?>
</div>
