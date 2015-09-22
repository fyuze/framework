<style tyle="text/css">
    html {
        font-family: Tahoma, Verdana, Segoe, sans-serif;
        font-size: 12px;
        position: relative;
        overflow: hidden;
    }

    #toolbar {
        border-top solid 1px #000000;
        position: absolute;
        bottom: 0;
        background: #454545;
        color: #fff;
        width: 100%;
        left: 0;
        right: 0;
        padding: 3px;

    }
    #toolbar ul {
        list-style: none;
        padding: 0px;
        margin: 0px;
    }
    #toolbar ul li {
        float: left;
        padding: 8px 12px;
        margin: 0px;
        border-right: solid 1px #000000;
    }
    #toolbar ul li.fyuze {
        float:right;
        border-right: 0;
    }
    #toolbar ul li.response-code {
        background-color: #2ca02c;
    }
    #toolbar ul li.danger {
        background-color: #ac2925;
    }
</style>

<div id="toolbar">
    <ul>
        <?php foreach($collectors as $collector): $tab = $collector->tab(); ?>
            <li class="<?php echo (array_key_exists('class', $tab) ? $tab['class'] : '')?>"><?=$tab['title'];?></li>
        <?php endforeach; ?>
        <li class="fyuze">Fyuze 0.1</li>
    </ul>
</div>
