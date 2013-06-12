<div class="IpTweets">
	<?php if(is_array($tweets) && count($tweets) > 0): ?>
		<ul>
			<?php foreach($tweets as  $tweet): ?>
				<li> 
					<?php echo $tweet->text; ?>
					<a href="<?php echo $tweet->tweetUrl; ?>" target="_blank" class="time"><?php echo $tweet->time; ?></a>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php else: ?>
		<h2>Twitter is temporarily down.</h2>
	<?php endif; ?>
</div>
