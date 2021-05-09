<div class="sub-section" style="order: {MODULE_POSITION};">
	<div class="content-container">
		<article id="{MODULE_NAME}-panel">
			<header class="module-header">
				<h2>{L_MODULE_TITLE}</h2>
				# IF C_MODULE_LINK #
					<div class="controls align-right">
						<a href="{PATH_TO_ROOT}/{MODULE_NAME}">{L_SEE_ALL_ITEMS}</a>
					</div>
				# ENDIF #
			</header>
			# IF C_NO_ITEM #
				<div class="content">
					<div class="message-helper bgc notice">
						${LangLoader::get_message('common.no.item.now', 'common-lang')}
					</div>
				</div>
			# ELSE #
				<div class="content">
					# START items #
						<div class="message-container message-small" itemscope="itemscope" itemtype="https://schema.org/CreativeWork">
							<div class="message-header-container">
								# IF C_AVATAR_IMG #<img class="message-user-avatar" src="{items.U_AVATAR_IMG}" alt="{items.PSEUDO}" /># ENDIF #
								<div class="message-header-infos">
									<div class="message-user-container">
										<h4>
											# IF items.C_AUTHOR_EXIST #
												<a class="{items.USER_LEVEL_CLASS}" href="{items.U_AUTHOR_PROFILE}"# IF items.C_USER_GROUP_COLOR # style="{items.USER_GROUP_COLOR}"# ENDIF #>{items.PSEUDO}</a>
											# ELSE #
												{items.PSEUDO}
											# ENDIF #
										</h4>
										# IF C_PARENT #
											<div class="controls message-user-infos-preview" aria-label="# IF C_TOPIC #{@module.posted.in.topic}# ELSE #{@module.posted.in.module}# ENDIF #">
												<a href="{items.U_TOPIC}"><i class="fa fa-fw # IF C_TOPIC #fa-file# ELSE #fa-cube# ENDIF #" aria-hidden="true"></i> {items.TOPIC}</a>
											</div>
										# ENDIF #
									</div>
									<div class="message-infos">
										<time datetime="{items.DATE_ISO8601}" itemprop="datePublished">{items.DATE}</time>
										<div class="message-action">
											<a href="{items.U_ITEM}"><i class="fa fa-share"></i> ${LangLoader::get_message('common.read.more', 'common-lang')}</a></div>
									</div>
								</div>
							</div>
							<div class="message-content flex-between">
								{items.CONTENT} ...
							</div>
						</div>
					# END items #
				</div>
			# ENDIF #
		</article>
	</div>
</div>
