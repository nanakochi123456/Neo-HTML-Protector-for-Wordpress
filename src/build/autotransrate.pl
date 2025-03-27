#!/usr/bin/env perl
# autotransrater
# https://github.com/yotiosoft/dptran/blob/main/README_JA.md

use strict;
use warnings;
use Getopt::Long;
use POSIX qw(strftime);

#my $DPTRAN='~/.cargo/bin/dptran';
my $DPTRAN='php build/deepl.php';
my $DPTRAN_USAGE=$DPTRAN . ' -u';

my $input_file="";
my $cache_file="";
my $from="";
my $to="";
my $table_file="language_table.txt";
my $package_name="test package";
my $email="example\@example.com";
my $version='0';

Getopt::Long::GetOptions(
    'input=s'	=> \$input_file,
    'from=s'	=> \$from,
	'to=s'		=> \$to,
	'cache=s'	=> \$cache_file,
	'table=s'	=> \$table_file,
	'package=s'	=> \$package_name,
	'email=s'	=> \$email,
	'version=s'	=> \$version
) or &usage();

if($input_file eq "" || $to eq "" || $table_file eq "" || $cache_file eq "") {
	&usage();
}

sub usage() {
	print <<EOM;
gettext autotransrate by \@nanakochi123456\n
Usage
perl autotransrate.pl (options) > output_file
--input=filename.pot
--from=Language Code (Default to automatic)
--to=Language Code
--cache=cache file
--table=language_table file
--package='Package Name'
--email=email\@example.org
--version=version
EOM
	exit;
}
my $from_table=&searchtable($table_file, $from);
my $to_table=&searchtable($table_file, $to);
my $from_lang = "";
my $to_lang = $to_table->{english};
if($from_table->{lang} ne "") {
	$from_lang = $from_table->{english};
} else {
	$from_lang = "Auto";
}

print STDERR "-----------------------------------------------\n";
print STDERR "autotranslate $from_lang to $to_lang\n";
#print STDERR `$DPTRAN_USAGE` . "\n";

&topbanner($from, $to, $input_file, $table_file, $package_name, $email, $version);
&pottransrate($from, $to, $table_file, $input_file, $cache_file);

sub pottransrate() {
	my($from, $to, $table, $input, $cache)=@_;
	my $from_table=&searchtable($table_file, $from);
	my $to_table=&searchtable($table_file, $to);
	my $from_lang = "";
	my $to_lang = $to_table->{lang};
	if($from_table->{lang} ne "") {
		$from_lang = $from_table->{lang};
	}

	open(my $fh, $input) or die "$input not found\n";

	my $multiline=0;
	my $str;
	my $count=0;
	foreach(<$fh>) {
		if($_ =~/^msgid\s+\"\"/) {
			$str="";
			$multiline=1;
		}

		if($multiline && $_ =~/^\"(.*?)\"/) {
			$str.=$1;
		}
		if($_ =~/^msgid\s+\"(.*?)\"/) {
			$str=$1;
		}
		if($_ =~/^msgstr\s+\"(.*?)\"/) {
			if($str ne "") {
				$multiline=0;
				my $tstr;
				my $cached;
				if($str=~/^http/) {
					$tstr=$to_table->{search};
				} else {
					($tstr, $cached)=&transrate($from_lang, $to_lang, $str, $cache);
				}

				$tstr=~s/Nanoh Yozakura/Nano Yozakura/g;
				if($str =~/ISIS/) {
					$tstr="ISIS chan";
				}

				if(!$cached) {
					print STDERR "--------------------------------\n";
					print STDERR "Detected [$str]\n";
					print STDERR "Transrate [$tstr]\n";
				}

				if(!($cached) && !($str=~/^http/)) {
					if($tstr ne "" && $tstr ne "Auth Key not set") {
						open(my $fh, ">>", $cache) || die "$cache can't write\n";
						print $fh "$str\t$tstr\n";
						close($fh);
					}
				}

				$tstr=~s/\"//g;
				print "msgid \"$str\"\n";
				print "msgstr \"$tstr\"\n";
				print "\n";
			}
			$multiline=0;
			$str="";
		}
	}
}

sub transrate() {
	my($from, $to, $str, $cache)=@_;
	my $tstr=&searchcache($str, $cache);
	if($tstr ne "") {
		return($tstr, 1);
	}

	my $from_table=&searchtable($table_file, $from);
	my $to_table=&searchtable($table_file, $to);

	my $from_lang = "";
	$from_lang = $from_table->{deepl};
	if($from_table->{deepl} ne "") {
		$from_lang = $from_table->{deepl};
	}
	my $to_lang = $to_table->{deepl};
	if($from_table->{deepl} ne "") {
		$from_lang = $from_table->{deepl};
	}

	my $cmd=$DPTRAN
		. ($from ne "" ? " --from=$from_lang" : "")
		. ($to ne "" ? " --to=$to_lang" : "")
		. " --input=\'$str\'";

	print STDERR $cmd . "\n";
	my $result = `$cmd`;
	$result=~s/[\r|\n]//g;
	$result=~s/\s\(.*//g;
	$result = ucfirst($result);
	return($result, 0);
}

sub searchcache() {
	my($str, $cache)=@_;
	if(open(my $fh, $cache)) {
		foreach(<$fh>) {
			s/[\r|\n]//g;
			if($_ ne '') {
				my @fields = split(/\t/, $_);
				next if scalar(@fields) != 2;
				my($ostr, $tstr)=@fields;
#print "---------------\nCache:\n$str\n$ostr\n$tstr\n";
				if($ostr eq $str && $tstr ne "") {
					close($fh);
					return $tstr;
				}
			}
		}
		close($fh);
	}
	return "";
}

sub searchtable() {
	my($table, $lang)=@_;
	open(my $fh, $table) or die "$table not found\n";
	foreach(<$fh>) {
		s/[\r|\n]//g;
		if($_ ne '') {
			my @fields = split(/\t/, $_);
			next if scalar(@fields) != 5;
			my ($deepl, $browser, $english, $japanese, $search)=@fields;
			if($deepl eq $lang || $browser eq $lang) {
				close($fh);
				my $ret={
					deepl=>$deepl,
					lang=>$browser,
					english=>$english,
					japanese=>$japanese,
					search=>$search
				};
				return $ret;
			}
		}
	}
	close($fh);
	die "Lang $lang not defined";
}

sub getdate() {
	my($time)=@_;
	my $current_time = strftime("%Y-%m-%d %H:%M", localtime($time));
	my $year = strftime("%Y", localtime($time));
	my $timezone = strftime("%z", localtime($time));
	my $formatted_timezone = $timezone =~ /(\+|\-)\d{4}/ ? $timezone : '+0000';
	$current_time . $formatted_timezone;
}

sub topbanner() {
	my($from, $to, $input, $table, $package, $email, $version)=@_;
	my ($sec, $min, $hour, $mday, $mon, $year) = localtime;
	$year += 1900;
	if(! -e $input) {
		die "$input not found\n";
	}
	my $mtime = (stat($input))[9];

my $ret=&searchtable($table_file, $to);
#print <<EOM;
#debug:deepl=@{[$ret->{deepl}]}
#debug:lang=@{[$ret->{lang}]}
#debug:english=@{[$ret->{english}]}
#debug:english=@{[$ret->{japanese}]}
#EOM

	print <<EOM;
# @{[$ret->{english}]} translations for $package package.
# Copyright (C) $year THE $package\'S COPYRIGHT HOLDER
# This file is distributed under the same license as the $package package.
#  <$email>, $year.
#
msgid ""
msgstr ""
"Project-Id-Version: $package $version\\n"
"Report-Msgid-Bugs-To: $email\\n"
"POT-Creation-Date: @{[ &getdate($mtime) ]}\\n"
"PO-Revision-Date: @{[ &getdate(time) ]}\\n"
"Last-Translator:  <$email>\\n"
"Language-Team: @{[$ret->{english}]}\\n"
"Language: @{[$ret->{lang}]}\\n"
"MIME-Version: 1.0\\n"
"Content-Type: text/plain; charset=UTF-8\\n"
"Content-Transfer-Encoding: 8bit\\n"
EOM

}
