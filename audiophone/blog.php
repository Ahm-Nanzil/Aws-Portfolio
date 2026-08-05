<?php
include 'header.php';
$page = pageData('blog')

?>

<div id="inner-wrap" class="wrap kt-clear">
		<section role="banner" class="entry-hero post-archive-hero-section entry-hero-layout-standard">
	<div class="entry-hero-container-inner">
		<div class="hero-section-overlay"></div>
		<div class="hero-container site-container">
			<header class="entry-header post-archive-title title-align-inherit title-tablet-align-inherit title-mobile-align-inherit">
					<h1 class="page-title post-home-title archive-title">
				<?php
					echo htmlspecialchars($page['blog']['title'] ?? 'Blog');

				?>
			</h1>
				</header><!-- .entry-header -->
		</div>
	</div>
</section><!-- .entry-hero -->
<div id="primary" class="content-area">
	<div class="content-container site-container">
		<main id="main" class="site-main" role="main">
							<div id="archive-container" class="content-wrap grid-cols post-archive grid-sm-col-2 grid-lg-col-3 item-image-style-above">
					
<article class="entry content-bg loop-entry post-1 post type-post status-publish format-standard hentry category-uncategorized">
		<div class="entry-content-wrap">
		<header class="entry-header">

			<div class="entry-taxonomies">
			<span class="category-links term-links category-style-normal">
				<a href="" rel="tag">Uncategorized</a>			</span>
		</div><!-- .entry-taxonomies -->
		<h2 class="entry-title"><a href="" rel="bookmark">Hello world!</a></h2><div class="entry-meta entry-meta-divider-dot">
	<span class="posted-by"><span class="meta-label">By</span><span class="author vcard"><a class="url fn n" href="">admin</a></span></span>					<span class="posted-on">
						<time class="entry-date published updated" datetime="2025-03-05T08:24:04+00:00">05/03/2025</time>					</span>
					</div><!-- .entry-meta -->
</header><!-- .entry-header -->
	<div class="entry-summary">
		<p>Welcome to WordPress. This is your first post. Edit or delete it, then start writing!</p>
	</div><!-- .entry-summary -->
	<footer class="entry-footer">
		<div class="entry-actions">
		<p class="more-link-wrap">
			<a href="" class="post-more-link">
				Read More<span class="screen-reader-text"> Hello world!</span><span class="kadence-svg-iconset svg-baseline"><svg aria-hidden="true" class="kadence-svg-icon kadence-arrow-right-alt-svg" fill="currentColor" version="1.1" xmlns="http://www.w3.org/2000/svg" width="27" height="28" viewBox="0 0 27 28"><title>Continue</title><path d="M27 13.953c0 0.141-0.063 0.281-0.156 0.375l-6 5.531c-0.156 0.141-0.359 0.172-0.547 0.094-0.172-0.078-0.297-0.25-0.297-0.453v-3.5h-19.5c-0.281 0-0.5-0.219-0.5-0.5v-3c0-0.281 0.219-0.5 0.5-0.5h19.5v-3.5c0-0.203 0.109-0.375 0.297-0.453s0.391-0.047 0.547 0.078l6 5.469c0.094 0.094 0.156 0.219 0.156 0.359v0z"></path>
				</svg></span>			</a>
		</p>
	</div><!-- .entry-actions -->
	</footer><!-- .entry-footer -->
	</div>
</article>
				</div>
						</main><!-- #main -->
			</div>
</div><!-- #primary -->
	</div>

<?php
include 'footer.php';
?>