<form>
    <label class="ipAdminLabel">Twitter @username</label>
    <input name="username" class="ipAdminInput" value="<?php echo htmlspecialchars((isset($username) && $username != '') ? $username : $default_username ); ?>" />

    <label class="ipAdminLabel">Number of tweets displayed</label>
    <input name="notweets" class="ipAdminInput" value="<?php echo htmlspecialchars((isset($notweets) && $notweets != '') ? $notweets : $default_notweets ); ?>" />
</form>