<section class="modal">
	<div class="head">
		<button class="red bare icon pill round action ripple-click" onClick="app.UI.modal.close()">&times;</button>
	</div>
	<div class="body">
		<p class="text"></p>
		<div class="form" style="display: none;"></div>
		<div class="action css-flex-gap-10">
			<div class="action-group">
				<button class="secondary ripple-click" onClick="app.UI.modal.close()">Dismiss</button>
			</div>
			<div class="action-group slider css-flex-wrap right"></div>
		</div>
	</div>
</section>
<section class="lightbox">
	<div class="bg-fade-dark" onClick="app.UI.lightbox.close(true)"></div>
	<div class="lb-box">
		<div class="head" style="display: none;">
			<p class="text txtoe"></p>
			<button class="red bare icon pill round action ripple-click" onClick="app.UI.lightbox.close()" style="display: none;">&times;</button>
		</div>
		<div class="body slider"></div>
	</div>
</section>
<nav class="context-menu slider">
	<ul class="link" hidden>
		<li data-action="open-URL-new-tab" onClick="app.IO.URL.open()"><i class="material-icons">open_in_new</i><span>Open link in new tab</span></li>
		<li data-action="copy-URL-address" onClick="app.IO.URL.copy()"><i class="material-icons">link</i><span>Copy link address</span></li>
	</ul>
	<ul class="nav">
		<li data-action="back" onClick="history.back()"><i class="material-icons">arrow_back</i><span>Back</span></li>
		<li data-action="reload" onClick="top.location.reload()"><i class="material-icons">refresh</i><span>Reload page</span></li>
		<li data-action="reload-frame" onClick="location.reload()"><i class="material-icons">refresh</i><span>Reload frame</span></li>
		<li data-action="next" onClick="history.forward()"><i class="material-icons">arrow_forward</i><span>Forward</span></li>
	</ul>
	<ul class="content" hidden>
		<li data-action="record-undo" onClick="app.IO.content.undo()"><i class="material-icons">undo</i><span>Undo</span></li>
		<li data-action="record-redo" onClick="app.IO.content.redo()"><i class="material-icons">redo</i><span>redo</span></li>
		<li data-action="content-cut" onClick="app.IO.content.cut()"><i class="material-icons">content_cut</i><span>Cut</span></li>
		<li data-action="content-copy" onClick="app.IO.content.copy()"><i class="material-icons">content_copy</i><span>Copy</span></li>
		<li data-action="content-paste" onClick="app.IO.content.paste()"><i class="material-icons">content_paste</i><span>Paste</span></li>
		<li data-action="content-clear" onClick="app.IO.content.clear()"><i class="material-icons">delete</i><span>Delete</span></li>
		<li data-action="select-all" onClick="app.IO.content.select(true)"><i class="material-icons">select_all</i><span>Select all</span></li>
	</ul>
	<ul class="action">
		<li data-action="print" onClick="print()"><i class="material-icons">print</i><span>Print page</span></li>
	</ul>
	<ul class="social">
		<li data-action="share" onClick="app.IO.share()"><i class="material-icons">share</i><span>Share this page</span></li>
		<li data-action="copyurl" onClick="app.IO.URL.link()"><i class="material-icons">language</i><span>Copy page URL</span></li>
	</ul>
</nav>
<aside class="notifications hscroll"></aside>
<aside class="back-to-top">
	<button class="blue bare icon action ripple-click" onClick="app.UI.scrollToTop()">
		<i class="material-icons">arrow_upward</i>
	</button>
</aside>
<section class="helping-tools"></section>