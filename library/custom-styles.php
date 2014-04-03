@import url(http://fonts.googleapis.com/css?family=<?php the_field('options_font_headline','option');?>|<?php the_field('options_font_body','option');?>);




body {
background-color:<?php the_field('dynamic_style_background_colour','option');?>;
font-family:'<?php the_field('options_font_body','option');?>';
}

h1 {
font-family:'<?php the_field('options_font_headline','option');?>';
}