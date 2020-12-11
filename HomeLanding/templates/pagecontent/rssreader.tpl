
<article id="rss-panel" style="order: {MODULE_POSITION};">
	<header>
		<h2>
			${Langloader::get_message('link.to.rss.site', 'common', 'HomeLanding')}
			<a href="{SITE_URL}" target="_blank">
				{SITE_TITLE}
			</a>
		</h2>
	</header>
	<div class="content">
		# IF C_ITEMS #
			<ul>
				# START items #

					<li>
						<span class="flex-between">
							<a class="big" href="{items.U_ITEM}" target="_blank" rel="noopener noreferrer">
								{items.TITLE}
							</a>
							<span class="small align-right">{items.DATE}</span>
						</span>
						<p>
							# IF items.C_HAS_THUMBNAIL #
								<img src="{items.U_THUMBNAIL}" class="align-left" alt="{items.TITLE}" />
							# ENDIF #
							{items.SUMMARY}# IF items.C_READ_MORE #...# ENDIF #
						</p>
					</li>

				# END items #
			</ul>
		# ELSE #
			{NO_ITEM}
		# ENDIF #
	</div>
	<footer></footer>
</article>
