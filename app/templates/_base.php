<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width">
        
        <title>Boo Compta</title>
        
        <link rel="stylesheet" href="/css/site.css">
    </head>
    <body>
        <?php if(View::data('message')): ?>
        <div id="message"><div id="message-content"><?php echo View::data('message'); ?></div></div>
        <?php endif; ?>
        <?php View::get('content') ?>
        
        <script type="text/javascript" src="/js/functions.js"></script>
        <script type="text/javascript" src="/js/site.js"></script>
    </body>
</html>