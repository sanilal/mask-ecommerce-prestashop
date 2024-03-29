{if $page.page_name == 'index'}
<div class="home_blog_post_area {$xipbdp_designlayout} {$hookName}">
	<div class="home_blog_post">
		<div class="page_title_area">
			{if isset($xipbdp_title)}
				<h3 class="page-heading">
					<span>{$xipbdp_title}</span>
				</h3>
			{/if}
			{if isset($xipbdp_subtext)}
				<p class="page_subtitle">{$xipbdp_subtext}</p>
			{/if}
			<div class="heading-line d_none"><span></span></div>
		</div>
		<div class="laberblog-i">
		<div class="row">
		{if (isset($xipblogposts) && !empty($xipblogposts))}
		<div class="home_blog_post_inner">
		
			{foreach from=$xipblogposts item=xipblgpst name=xipblg}
				<div class="item-inner">
					<div class="item">
						<article class="blog_post">
							<div class="blog_post_content">
									<div class="blog_post_content_top">
										
										<div class="post_thumbnail laberMedia-body">
											{if $xipblgpst.post_format == 'video'}
												{assign var="postvideos" value=','|explode:$xipblgpst.video}
												{if $postvideos|@count > 1 }
													{include file="module:xipblog/views/templates/front/post-video.tpl" videos=$postvideos width='570' height="570" class="carousel"}
												{else}
													{include file="module:xipblog/views/templates/front/post-video.tpl" videos=$postvideos width='570' height="570" class=""}
												{/if}
											{elseif $xipblgpst.post_format == 'audio'}
												{assign var="postaudio" value=','|explode:$xipblgpst.audio}
												{if $postaudio|@count > 1 }
													{include file="module:xipblog/views/templates/front/post-audio.tpl" audios=$postaudio width='570' height="570" class="carousel"}
												{else}
													{include file="module:xipblog/views/templates/front/post-audio.tpl" audios=$postaudio width='570' height="570" class=""}
												{/if}
											{elseif $xipblgpst.post_format == 'gallery'}
												{if $xipblgpst.gallery_lists|@count > 1 }
													{include file="module:xipblog/views/templates/front/post-gallery.tpl" gallery=$xipblgpst.gallery_lists imagesize="home_default" class="carousel"}
												{else}
													{include file="module:xipblog/views/templates/front/post-gallery.tpl" gallery=$xipblgpst.gallery_lists imagesize="home_default" class=""}
												{/if}
											{else}
												<a class="thumbnail" href="{$xipblgpst.link}">
													<img class="xipblog_img img-responsive" src="{$xipblgpst.post_img_large}" alt="{$xipblgpst.post_title}">
												</a>
												<div class="blog_mask">
													<div class="blog_mask_content">
														<a class="thumbnail_lightbox" href="{$xipblgpst.post_img_large}">
															<i class="icon_plus"></i>
														</a>
													</div>
												</div>
											{/if}
										</div>
										
									</div>
							
								
									<div class="blog_post_content_bottom">
											<h3 class="post_title"><a href="{$xipblgpst.link}">{$xipblgpst.post_title}</a></h3>
											<div class="post_meta clearfix">
												<p class="meta_author">
													 <i class="icon-user"></i> 
													{l s='Posted by' d='Shop.Theme.Laberthemes'}
													<span>{$xipblgpst.post_author_arr.firstname} {$xipblgpst.post_author_arr.lastname}</span>
												</p>
												
												
												<p class="meta_category">
													 <i class="icon-tag"></i> 
													{l s='IN' d='Shop.Theme.Laberthemes'}
													<a href="{$xipblgpst.category_default_arr.link}">{$xipblgpst.category_default_arr.name}</a>
												</p>
											</div>
											<div class="post_content">
											{if isset($xipblgpst.post_excerpt) && !empty($xipblgpst.post_excerpt)}
												<p>{$xipblgpst.post_excerpt|truncate:100:' ...'|escape:'html':'UTF-8'}</p>
											{else}
												<p>{$xipblgpst.post_content|truncate:100:' ...'|escape:'html':'UTF-8'}</p>
											{/if}
											</div>
										
										{*<p class="meta_date">
											<span><i class="fa fa-calendar"></i> {$xipblgpst.post_date|date_format:"%B"} {$xipblgpst.post_date|date_format:"%d"|truncate:3} {$xipblgpst.post_date|date_format:"%Y"}</span>
											
										</p>
										
										
										
										
										<div class="content_more">
											<a class="read_more" href="{$xipblgpst.link}">{l s='Read more' d='Shop.Theme.Laberthemes'} <i class="fa fa-arrow-circle-right"></i></a>
										</div>*}
									</div>
							
							</div>
						</article>
					</div>
				</div>
			{/foreach}
		</div>
		<div class="owl-buttons">
			<p class="owl-prev prevBlog_post_inner"><i class="pe-7s-angle-left"></i></p>
			<p class="owl-next nextBlog_post_inner"><i class="pe-7s-angle-right"></i></p>
		</div>
		{else}
			<p>{l s='No Blog Post Found' d='Shop.Theme.Laberthemes'}</p>
		{/if}
		</div>
		
		</div>
	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		var owl = $(".home_blog_post_inner");
		owl.owlCarousel({
			items : 3,
			itemsDesktop : [1199,2],
			itemsDesktopSmall : [991,2],
			itemsTablet: [767,2],
			itemsMobile : [480,1],
			rewindNav : false,
			autoPlay :  false,
			stopOnHover: false,
			pagination : false,
		});
		$(".nextBlog_post_inner").click(function(){
		owl.trigger('owl.next');
		})
		$(".prevBlog_post_inner").click(function(){
		owl.trigger('owl.prev');
		})
	});
</script>
{/if}