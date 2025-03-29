<aside class="navigator_tab slider" opened="<?php echo($_COOKIE['sui_open-nt'])??"false"; ?>">
    <?php require($navtabpath??"aside-navigator.php"); ?>
</aside>
<section class="modal">
	<label onClick="app.ui.modal.close()" class="ripple-click">⨯</label>
	<span class="ctxt"></span>
	<div>
		<span><a role="button" class="ripple-click" onClick="app.ui.modal.close()" href="javascript:void(0)" data-text="Dismiss"></a></span>
		<span class="slider"><a role="button" class="filled ripple-click"></a></span>
	</div>
</section>
<section class="lightbox">
	<span class="fadebg" data-dark="false"></span>
	<div class="displayer"></div>
</section>
<nav class="cm">
    <ul class="nav">
        <li class="back" onClick="window.history.back()"><i class="material-icons">arrow_back</i><span>Back</span></li>
        <li class="reload" onClick="location.reload()"><i class="material-icons">refresh</i><span>Reload page</span></li>
        <li class="next" onClick="window.history.forward()"><i class="material-icons">arrow_forward</i><span>Forward</span></li>
    </ul>
    <div class="divider"></div>
    <ul class="social">
        <li class="share" onClick="app.io.share.now()"><i class="material-icons">share</i><span>Share this page</span></li>
        <li class="copyurl" onClick="app.io.copy.location()"><i class="material-icons">content_copy</i><span>Copy page URL</span></li>
    </ul>
    <div class="divider"></div>
    <ul class="action">
        <li class="print" onClick="window.print()"><i class="material-icons">print</i><span>Print page</span></li>
    </ul>
</nav>
<nav class="rfr txtoe"></nav>
<aside class="fm hscroll"></aside>
<aside class="up">
    <a onClick="app.io.scrollToTop()" href="javascript:void(0)">
        <div class="ripple-click"><i class="material-icons">arrow_upward</i></div>
    </a>
</aside>