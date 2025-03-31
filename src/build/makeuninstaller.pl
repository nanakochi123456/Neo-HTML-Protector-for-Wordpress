#!/usr/bin/env perl

use strict;
use warnings;

my $dir = '/path/to/directory';
my @php_files = glob("classes/*.php");

my %opt;
foreach my $file (@php_files) {
	#print "$file\n";
	if(open(my $fh, $file)) {
		foreach(<$fh>) {
			s/[\r|\n]//g;
			if(/get_option\s*\(\s*['"]([^'#]+)['"]/) {
				$opt{$1}="1";
			}
		}
		close($fh);
	}
}

print "<?php\n";
print "// Neo HTML Protector delete options\n";
print "// This is auto generate file\n";
print "\n";
print "defined('ABSPATH') || defined('WP_UNINSTALL_PLUGIN') || die('Oh! No!');\n";
print "\n";

print "function neohp_delete_options() {\n";
foreach my $key (sort keys %opt) {
    print "	delete_option('$key');\n";
}
print "}\n";
