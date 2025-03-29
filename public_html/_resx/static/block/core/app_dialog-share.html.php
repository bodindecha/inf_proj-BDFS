<div class="sharer">
	<style type="text/css">
		.sharer table { width: 100%; }
		.sharer table tr td {
			--abtn-height: 25px;
			padding: 5px;
		}
		.sharer table tr td a {
			text-align: left;
			display: flex;
		}
		.sharer table tr td a img, .sharer table tr td a i {
			width: var(--abtn-height); height: var(--abtn-height);
			font-size: 24px; line-height: var(--abtn-height); text-decoration: none;
			color: var(--clr-main-white-absolute); text-align: center;
			object-fit: contain;
		}
		.sharer table tr td a span {
			margin-left: 7.5px;
			font-size: 15px; line-height: var(--abtn-height); font-weight: bold;
		}
		.sharer center { margin: 10px 0; }
		.sharer center button {
			padding: 2.5px 20px;
			font-size: 15px; font-weight: bold;
		}
	</style>
	<table><tbody>
		<tr>
			<td><a onClick="app.IO.share.to('facebook')" href="javascript:" role="button" class="gray"><img src="<?=$APP_CONST["cdnURL"]?>static/img/social/facebook.png"><span>Facebook</span></a></td>
			<td><a onClick="app.IO.share.to('twitter')" href="javascript:" role="button" class="gray"><img src="<?=$APP_CONST["cdnURL"]?>static/img/social/twitter.png"><span>Twitter</span></a></td>
		</tr>
		<tr>
			<td><a onClick="app.IO.share.to('google-plus')" href="javascript:" role="button" class="gray"><img src="<?=$APP_CONST["cdnURL"]?>static/img/social/google-plus.png"><span>Google+</span></a></td>
			<td><a onClick="app.IO.share.to('linked-in')" href="javascript:" role="button" class="gray"><img src="<?=$APP_CONST["cdnURL"]?>static/img/social/linked-in.png"><span>LinkedIn</span></a></td>
		</tr>
		<tr>
			<td><a onClick="app.IO.share.to('email')" href="javascript:" role="button" class="gray"><i class="material-icons">email</i><span>Email</span></a></td>
			<td><a onClick="app.IO.share.to('copyURL')" href="javascript:" role="button" class="gray"><i class="material-icons">link</i><span>Copy URL</span></a></td>
		</tr>
	</tbody></table>
	<center><button onClick="app.IO.share.cancel()" class="red">Cancel</button></center>
</div>