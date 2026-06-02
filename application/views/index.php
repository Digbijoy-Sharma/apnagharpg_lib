<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->

<head>
    <meta charset="utf-8" />
    <title><?php echo $this->db->get_where('setting', array('name' => 'system_name'))->row()->content; ?> - <?php echo $this->db->get_where('setting', array('name' => 'tagline'))->row()->content; ?> | <?php echo $page_title; ?></title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <meta content="<?php echo $this->db->get_where('setting', array('name' => 'tagline'))->row()->content; ?>" name="description" />
    <meta content="t1m9m.com" name="author" />

    <link rel="icon" type="image/*" href="<?php echo base_url(); ?>uploads/website/<?php echo $this->db->get_where('setting', array('name' => 'favicon'))->row()->content; ?>">

    <?php include 'includes_top.php'; ?>
</head>

<body>
    <!-- begin #page-loader -->
    <div id="page-loader" class="fade show">
        <div class="material-loader">
            <svg class="circular" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"></circle>
            </svg>
            <div class="message">Loading...</div>
        </div>
    </div>
    <!-- end #page-loader -->

    <!-- begin #page-container -->
    <div id="page-container" class="fade page-sidebar-fixed page-header-fixed page-with-wide-sidebar">

        <?php include 'header.php'; ?>
        <?php include 'sidebar.php'; ?>
        <?php include $page_name . '.php'; ?>

        <?php include 'modal.php'; ?>
    </div>
    <!-- end page container -->

    <?php include 'includes_bottom.php'; ?>
    <?php include 'toastr.php'; ?>
    
    
    
<script>
    var url = 'https://wati-integration-service.clare.ai/ShopifyWidget/shopifyWidget.js?74163';
    var s = document.createElement('script');
    s.type = 'text/javascript';
    s.async = true;
    s.src = url;
    var options = {
  "enabled":true,
  "chatButtonSetting":{
      "backgroundColor":"#4dc247",
      "ctaText":"",
      "borderRadius":"25",
      "marginLeft":"0",
      "marginBottom":"50",
      "marginRight":"50",
      "position":"right"
  },
  "brandSetting":{
      "brandName":"WATI",
      "brandSubTitle":"Typically replies within a day",
      "brandImg":"https://cdn.clare.ai/wati/images/WATI_logo_square_2.png",
      "welcomeText":"Hi there!\nHow can I help you?",
      "messageText":"Hello, I have a question about {{page_link}}",
      "backgroundColor":"#0a5f54",
      "ctaText":"Start Chat",
      "borderRadius":"25",
      "autoShow":false,
      "phoneNumber":"9756056848"
  }
};
    s.onload = function() {
        CreateWhatsappChatWidget(options);
    };
    var x = document.getElementsByTagName('script')[0];
    x.parentNode.insertBefore(s, x);
</script>
</body>

</html>