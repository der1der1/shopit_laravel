<footer id="footer">
	<div id="editing_page"><a href="{{route('edit_show')}}" title="管理頁面。管理者登入才會顯示">
			go to Editing Page</a></div>
	<div id="author">
		<p>本網站由德斯貿易公司(Desmo co.,lmt.)所有 Copy Right &copy; 2023</p>
	</div>
	<div>開發者：謝德一 deniel87deniel87@gmail.com</div>
	<div id="cont"><a href="{{route('report_show')}}">Contact Us</a> </div>
</footer>

<footer id="footer_small">
	<div id="editing_page"><a href="{{route('edit_show')}}" title="管理頁面。管理者登入才會顯示">
			go to Editing Page</a></div>
	<div id="author">
		<p>本網站由德斯貿易公司(Desmo co.,lmt.)所有 Copy Right &copy; 2023</p>
	</div>
	<div>開發者：謝德一 deniel87deniel87@gmail.com</div>
	<div id="cont"><a href="{{route('report_show')}}">Contact Us</a> </div>
</footer>

<style>
	:root {
		--white: #FFF8DC;
		--background: #FFFFFF;
		--box: #F6F6F6;
		--box2: #FBE0C5;
		--text: #40210F;
		--text2: #2A2A2A;
		--line: #40210F;
		--btnline: #FFFFFF;
		--background2: #FBE0C5;
		--btn: #2A2A2A;
		--btnhover: #D96253;
	}

	a {
		text-decoration: none;
		color: var(--text);
	}

	a:hover {
		text-decoration: none;
	}

	#footer {
		margin-bottom: -50px;
		height: 150px;
		width: 100%;
		padding-top: 40px;
		display: flex;
		justify-content: space-around;
		color: var(--text);
		background-color: var(--background2);
		font-size: 10px;
	}

	#footer_small {
		display: none;
	}

	#cont,
	#editing_page {
		padding-top: 10px;
		color: slategrey;
		text-decoration: none;
	}

	@media(max-width:700px) {
		#footer {
			display: none;
		}

		#footer_small {
			display: block;
			margin-bottom: -50px;
			height: 150px;
			width: 100%;
			padding-top: 15px;
			display: flex;
			color: var(--text);
			background-color: var(--background2);
			font-size: 10px;
			flex-direction: column;
			align-items: center;
		}
	}
</style>