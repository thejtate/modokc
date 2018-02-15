<?php //init backend?>
<?php include_once(__DIR__ . '/backend/main.php'); ?>
<?php $form = contact_form();?>
<?php $submission_data = process_form($form);?>
<?php //init backend?>
<?php $title = 'email-form'; ?>
<?php include 'tpl/includes/head.inc'; ?>
<body class="page page-email">
<div class="outer-wrapper">
  <div class="content-wrapper site-container">
    <div class="b-email">
      <div class="inner">
        <div class="title">Speak up for:</div>
        <ul>
          <li>Full strength beer & wine in grocery & convenience stores</li>
          <li>Cold beer & wine in liquor stores</li>
          <li>Single strength beer (phase out of 3.2% ABW)</li>
        </ul>
        <div class="items">
          <div class="item"><img src="theme/images/tmp/top-item-1-a.png" alt=""/></div>
          <div class="item"><img src="theme/images/tmp/top-item-2-a.png" alt=""/></div>
          <div class="item"><img src="theme/images/tmp/top-item-3-a.png" alt=""/></div>
        </div>
        <div class="form form-email">
          <form action="" method="POST">
            <?php //Form submission messages.?>
            <?php if(!empty($submission_data)): ?>
              <div id="notice-wrapper" class="<?php print (!empty($submission_data['status']) && $submission_data['status'] == 'error' ? 'error' : 'status') ?>">
                <div class="notice">
                  <?php foreach($submission_data as $key => $message): ?>
                    <?php if($key !== 'status' && $key !== 'field'): ?>
                      <?php echo $message;?><br>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>
            <?php //Form submission messages.?>
            <div class="form-item form-type-text">
              <input type="text" class="form-text" placeholder="Full Name*" name="name" <?php print_value('name', $submission_data);?>/>
            </div>
            <div class="form-item form-type-text">
              <input type="text" class="form-text" placeholder="Email*" name="email" <?php print_value('email', $submission_data);?>/>
            </div>
            <div class="form-item form-type-text width-80">
              <input type="text" class="form-text" placeholder="Address" name="address" <?php print_value('address', $submission_data);?>/>
            </div>
            <div class="form-item form-type-text width-20">
              <input type="text" class="form-text <?php print_error_class('zip', $submission_data) ?>" placeholder="Zip*" maxlength="5" name="zip" <?php print_value('zip', $submission_data);?>/>
            </div>
            <div class="text">
              <p>Dear Official,</p>
              <p>
              I am writing to urge you to vote YES on SJR 68 and YES on SB 383. I support modern alcohol laws, and I believe Oklahomans should be given a chance to vote to allow liquor stores, grocery stores and convenience stores to  sell cold full strength beer and wine.
              </p>

            <p>
            SJR 68 and SB 383 expand consumer choice and convenience, and will ultimately lead to more jobs and economic growth. Please help to update Oklahoma’s outdated, prohibition-era alcohol laws by supporting these key pieces of legislation.
            </p>

            </div>
            <div class="captcha">
              <img src="backend/libraries/captcha/captcha.php" id="captcha2" />
              <div class="reload-captcha-wrapper">
                <a href="#" class="funnel-reload-captcha" onclick="
                  url = 'backend/libraries/captcha/captcha.php?'+Math.random();
                document.getElementById('captcha2').src=url;
                document.getElementById('field-captcha2').focus(); return false;">Generate a
                  new captcha</a></div>
              <div class="form-item form-type-textfield form-item-captcha-response">
                <label for="edit-captcha-response">Enter the characters shown in the image.
                  <span class="form-required">*</span>
                </label>
                <input type="text" size="15" maxlength="128" class="form-text required" name="captcha" id="field-captcha2">
              </div>
            </div>
            <div class="form-item form-type-checkbox">
              <input type="checkbox" class="form-checkbox" id="checkbox" name="send_me" value="Yes" <?php print_value('send_me', $submission_data);?>/>
              <label for="checkbox">Send me emails about this campaign</label>
            </div>
            <div class="form-actions">
              <input type="submit" value="Send" name="contact_submit"/>
            </div>
            </form>
        </div>
      </div>
      <div class="img"><img src="theme/images/tmp/img.jpg" alt=""/></div>
    </div>
  </div>
</div>
</body>
</html>