#http://stackoverflow.com/questions/7239801/plot-histogram-with-specified-patterns-for-different-categories-in-gnuplot
#http://gnuplot.sourceforge.net/demo/fillstyle.html
set title "A demonstration of boxes in mono with style fill pattern"
set samples 11
set boxwidth 0.5 
set style fill pattern border
plot [-2.5:4.5] 100/(1.0+x*x) title 'pattern 0' with boxes lt -1, \
                 80/(1.0+x*x) title 'pattern 1' with boxes lt -1, \
                 40/(1.0+x*x) title 'pattern 2' with boxes lt -1, \
                 20/(1.0+x*x) title 'pattern 3' with boxes lt -1
