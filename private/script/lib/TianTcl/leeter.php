<?php
	class Leet {
		private static $o = array("leet" => "", "lame" => ""),
			$l = "",
			$t = 1,
			$k = false;

		// Function a(): Performs a series of substitutions.
		private static function a() {
			self::d("pwn","own");
			self::d(" ownzor"," own");
			self::g(" is good "," owns ");
			self::g(" are good "," own ");
			self::g(" am good "," own ");
			self::d("good you","better than you");
			self::d("good me","better than me");
			self::d("good them","better than them");
			self::d("good him","better than him");
			self::d("good her","better than her");
			self::d("good it","better than it");
			self::d("good us","better than us");
			self::d("good that","better than that");
			self::d("good all","better than all");
			self::g(" defeated "," owned ");
			self::d("my are good","my own");
			self::d("your are good","your own");
			self::d("their are good","their own");
			self::d("our are good","our own");
			self::d("her are good","her own");
			self::d("his are good","his own");
			self::g(" are "," r ");
			self::g(" am "," m ");
			self::g("unhack","uhaxor");
			self::g("hacker","haxor");
			self::d("hackerer","hacker");
			self::g("excellent","xellent");
			self::g(" are you "," ru ");
			self::g("hack","haxor");
			self::g("penis","penor");
			self::d(" pwn "," own ");
			self::g(" yay "," woot ");
			self::g(" you"," joo");
			self::d(" yor "," your ");
			self::g("speak","speek");
			self::g("leet","1337");
			self::g("internet","big lan");
			self::g(" picture"," pixor");
			self::g("n   [^]   t ","   [^]   nt ");
			self::g(" kill"," frag");
			self::g(" lamer "," llama ");
			self::g(" newbie "," noob ");
			self::g(" sex "," sexor ");
			self::g(" technique "," tekniq ");
			self::g("quake","quaek");
			self::g(" rock "," roxor ");
			self::g(" rocks "," roxorez ");
			self::g("cool","kewl");
			self::g(" the "," teh ");
			self::g("ass","azz");
			self::g("cum","spooge");
			self::g("ejaculate","spooge");
			self::g("fuck","fuxor");
			self::g("phuck","phuxor");
			self::g("porn","pron");
			self::g("dude","dood");
			self::g(" me "," meh ");
			self::g(" with "," wit ");
			self::g(" oh my god "," omg ");
			self::d(" omfg "," oh my f*cking god ");
			self::g(" oh my fucking god "," omfg ");
			self::g(" oh my phoxoring god "," omfg ");
			self::d("wtf","what the f*ck");
			self::g(" what the fuck "," wtf ");
			self::d(" roflmao "," rolling on the floor laughing my ass off ");
			self::d(" rofl "," rolling on the floor laughing ");
			self::g(" laugh my ass off "," lmao ");
			self::g(" okay "," kk ");
			self::g(" thanks "," thx ");
			self::g("rude","rood");
			self::g("ness ","nees ");
			self::g("please","pleez");
			self::g("money","lewt");
			self::d("loot","money");
			self::g("qu","kw");
			self::g("fear","fjeer");
			self::g(" because "," cuz ");
			self::g("more elite","eliteer");
			self::g(" an a"," a a");
			self::g(" an e"," a e");
			self::g(" an i"," a i");
			self::g(" an o"," a o");
			self::g(" an u"," a u");
			self::g("bitch","bizotch");
			self::g("suck","suxor");
			self::g("at ","@ ");
			self::g(" e@ "," eat ");
			if (self::$t == 1) self::g("e@","eat");
			self::g("elite","leet");
			self::g(" computers "," boxen ");
			self::g(" computer "," boxor ");
			self::g(" you "," u ");
			self::g(" your"," ur");
			self::g(" loot "," lewt ");
			self::g(" stuff "," lewt ");
			self::g(" fool "," foo ");
			self::g(" yo "," jo ");
			self::g("ks ","x ");
			self::g("se ","ze ");
			self::g("nigga","nigzor");
			self::g("nigger","nigzor");
			self::g("negro","nigzor");
			self::d("ah ","er ");
			self::d("yeer","yeah");
			self::g("ing ","in   [^]	");
			self::g("very gay","gheyzor");
			self::g(" f"," ph");
			self::g("ash ","# ");
			self::g(" cu"," ku");
			self::g(" ca"," ka");
			self::g(" cat"," kat");
			self::g(" co"," ko");
			self::g("s ","z ");
			self::g("sz ","ss ");
			self::d(" ph"," f");
			self::d(" ghey "," gay ");
			self::d("badways","horribly");
			self::d(" ownzor"," own");
			self::d("kthxbye","okay. thanks. bye.");
			if (self::$t == 1) self::g("kk thx bye","kthxbye");
			self::d(" k "," okay ");
			self::d(" thx "," thanks ");
			self::d(" i are "," i am ");
			self::d(" hacker it "," hack it ");
			self::d(" hacker them "," hack them ");
			self::d(" hacker her "," hack her ");
			self::d(" hacker him "," hack him ");
			self::d(" hacker a "," hack a ");
			self::d(" hacker his "," hack his ");
			self::d(" hacker their "," hack their ");
			self::d(" hacker that "," hack that ");
			self::d(" qea "," Quake 3 Arena ");
			self::d(" qe "," Quake 3 ");
			self::d(" l "," 1 ");
			self::d(" z "," 2 ");
			self::d(" e "," 3 ");
			self::d(" s "," 5 ");
			self::d(" g "," 6 ");
			self::d(" l "," 7 ");
			self::d(" b "," 8 ");
			self::d(" y "," 9 ");
			self::d(" o "," 0 ");
			self::d(" L "," 1 ");
			self::d("   [^]   5","   [^]   s");
			self::d("siow","slow");
			self::d("ciear","clear");
			self::d("titie","title");
			self::d(" da "," the ");
			self::d(" dah "," the ");
			self::d("aiso","also");
			self::d("eii","ell");
			self::d("ii","ll");
			self::d("!i ","!! ");
			self::d(" ! "," i ");
			self::d("eip","elp");
			self::d("sz ","ss ");
			self::d("uks ","ucks ");
			self::d("eer","ear");
			self::d("!!s","lis");
			self::d("o/o","");
			self::d("eie","ele");
			self::d("zor","er");
			self::d("!!ing","lling");
			self::d("w!!!","will");
			self::d("wh!!e","while");
			self::d("piay","play");
			self::d("auit","ault");
			self::d("ibie","ible");
			self::d("tah","ter");
			self::d("fah","fer");
			self::d("ouid","ould");
			self::d("a!!y","ally");
			self::d(" cus "," cuz ");
			self::d("iot","lot");
			self::d("oia","ola");
			self::d("zn","sn");
			self::d("siat","slat");
			self::d(" fone"," phone");
			self::d(" fase"," phase");
			self::d(" farmac"," pharmac");
			self::d(" fenom"," phenom");
			self::d(" fobia"," phobia");
			self::d(" foto"," photo");
			self::d(" fk"," fuck");
			self::d("elitear","more elite");
			self::d("worid","world");
			self::d("dewd","dude");
			self::d("eleet","elite");
			self::d("iam","lam");
			self::d("@ ","at ");
			self::d("@","a");
			self::d("i{","k");
			self::d("#","h");
			self::d("iis","r");
			if (self::$t == 2) {
				self::$l = preg_replace('/(can|should|would|could|have|did) (?:are|is|am) good/', '$1 defeat', self::$l);
				self::$l = preg_replace('/(can|should|would|could|have|did|do|will|shall) (?:are|is|am) (good|better than)/', '$1 defeat', self::$l);
				self::$l = preg_replace('/(?:are|is|am) good (me|you|him|her|them|y\'all|my|your|his|her|their|our)/', 'defeat $1', self::$l);
			}
		}

		// Function r(): Performs another series of substitutions.
		private static function r() {
			self::g("a","4");
			self::g("b","8");
			self::g("e","3");
			self::g("g","9");
			self::g("i","1");
			self::g("o","0");
			self::g("s","5");
			self::g("t","7");
			self::g("z","2");
			self::g("d","|)");
			self::g("f","|=");
			self::g("h","|-|");
			self::g("ll","|_|_");
			self::g("u","|_|");
			self::g("l","|_");
			self::g("j","_|");
			self::g("k","|<");
			self::g("m","|\\/|");
			self::g("n","|\\|");
			self::g("v","\\/");
			self::g("w","\\|/");
			self::g("x","><");
			self::g("y","`/");
			self::d("'/","y");
			self::d("/v\\","m");
			self::g("p","|>");
			self::g("q","().");
			self::g("r",".-");
			self::g("c","(");
			self::d("o|o","do");
			self::d("|o","b");
			self::d("o|","d");
			self::g("t","+");
			self::g("g","6");
			self::g("w","\\/\\/");
			self::g("w","vv");
			self::g("k","/<");
			self::g("s","$");
			if (self::$t == 2) self::g("i","!");
			self::g("m","|v|");
			self::g("mc","|vk");
			self::g("w","\\^/");
			self::g("c","<");
			self::g("i","|");
			self::g("y","\\-/");
			self::g("h","}{");
			if (self::$t != 1) {
				self::g("t","†");
				self::g("u","µ");
				self::g("c","©");
				self::g("c","¢");
				self::g("b","ß");
				self::g("r","®");
				self::g("f","ƒ");
				self::g("x","><");
				self::g("e","3");
				self::g("d","Ð");
				self::g("d","ð");
				self::g("v","v");
				self::g("t","‡");
				self::g("l","£");
				self::g("z","ž");
				self::g("y","¥");
				self::g("n","ñ");
				self::g("x","×");
				self::g("?","¿");
				self::d("¡","i");
				if (self::$t == 2) {
					self::$l = preg_replace('/(?!e)@(\B)/', 'at$1', self::$l);
					self::d("@","a");
					self::$l = preg_replace('/iz(?!e)(\w)/', 'r$1', self::$l);
					self::$l = preg_replace('/iy/', 'ly', self::$l);
					self::$l = preg_replace('/(\s|\.|,|\?|!)uk/', '$1lik', self::$l);
					self::d("i-i","h");
					self::$l = preg_replace('/i(a|\@)te/', 'late', self::$l);
					self::d("eei","eel");
					self::d("iee","lee");
					self::d("eio","elo");
					self::d("wlli","will");
					self::d("ioo","loo");
					self::d("d\\ll","oni");
					self::d("d\\ii","oni");
					self::d("i-b","ho");
					self::d("lld","ild");
					self::d(" unk"," link");
					self::d("llim","lum");
					self::d("/v","n");
					self::d("milumeter","millimeter");
					self::d("skllis","skills");
					self::d("u>","lp");
					self::$l = preg_replace('/dj(\w)/', 'ou$1', self::$l);
					self::$l = preg_replace('/(\w)dj/', '$1ou', self::$l);
					self::$l = preg_replace('/dc(\w)/', 'ok$1', self::$l);
					self::$l = preg_replace('/(\w)dc/', '$1ok', self::$l);
					self::d("d_","ol");
					self::d("i\\b","no");
				}
			}
		}

		// Function e(): Converts input text to leet.
		private static function e($e) {
			self::$o["lame"] = $e;
			self::$t = 1;
			self::$l = " ".self::$o["lame"]." ";
			self::$l = strtolower(self::$l);
			self::u();
			self::a();
			self::r();
			self::c();
			self::$o["leet"] = trim(strtorandom(self::$l));
			return self::$o["leet"];
		}

		// Function h(): Converts leet text back to normal text.
		private static function h($e) {
			self::$o["leet"] = $e;
			self::$t = 2;
			self::$l = " ".self::$o["leet"]." ";
			self::$l = strtolower(self::$l);
			self::$l = preg_replace('/(\s)!(\s)/', '$1i$2', self::$l);
			self::$l = preg_replace('/!+\W/', '.', self::$l);
			self::r();
			// Anonymous function to perform additional modifications if necessary.
			if (strlen(self::$l) >= 15) {
				$e = 0;
				for ($i = 0; $i < strlen(self::$l); $i++) {
					if (substr(self::$l, $i, 1) == " ") $e++;
				} if (0.5 <= $e / strlen(self::$l)) {
					self::s("  ","##");
					self::s(" ","");
					self::s("##"," ");
					$o = 0;
					$a = 0;
					for ($i = 0; $i < strlen(self::$l); $i++) {
						if (substr(self::$l, $i, 1) == " ") {
							if ($o < $a) $o = $a;
							$a = 0;
						} else $a++;
					} if (!self::$k && 10 < $o) self::$k = true;
				}
			}
			self::u();
			self::a();
			self::c();
			self::$o["lame"] = strtorandom(self::$l);
			return self::$o["lame"];
		}

		// Function u(): Replaces certain punctuation with unique markers.
		private static function u() {
			self::s(".", "   [%]   ");
			self::s(",", "   [@]   ");
			self::s("?", "   [©]   ");
			self::s("!", "   [$]   ");
			self::s('"', "   [&]   ");
			self::s("'", "   [^]   ");
			self::s(")", "   [~]   ");
			self::s("\n", "   [*]   ");
			self::s("\r", "");
		}

		// Function c(): Converts unique markers back to their original punctuation.
		private static function c() {
			self::s("   [%]   ", ".");
			self::s("   [@]   ", ",");
			self::s("   [a]   ", ",");
			self::s("   [©]   ", "?");
			self::s("   [$]   ", "!");
			self::s("   [&]   ", '"');
			self::s("   [^]   ", "'");
			self::s("   [~]   ", ")");
			self::s("   [*]   ", "\n");
		}

		// Function s(): Replaces all occurrences of $e with $o in self::$l.
		private static function s($e, $o) {
			$r = 0;
			$a = self::$l;
			while (strpos($a, $e) !== false) {
				$r++;
				if ($r > 200) break;
				$pos = strpos($a, $e);
				$a = substr($a, 0, $pos).$o.substr($a, $pos + strlen($e));
			} self::$l = $a;
		}

		// Function g(): Performs substitutions based on the current mode ($t).
		private static function g($e, $o) {
			if (self::$t == 1 && (mt_rand() / mt_getrandmax()) <= 0.8) self::s($e, $o);
			if (self::$t == 2) self::s($o, $e);
		}

		// Function d(): Performs substitutions only when self::$t equals 2.
		private static function d($e, $o) {
			if (self::$t <> 2) return;
			self::s($e, $o);
		}

		// Public method "from": Converts normal text to leet.
		final public static function from($input) {
			return self::e($input);
		}

		// Public method "totxt": Converts leet text back to normal text.
		final public static function totxt($input) {
			return self::h($input);
		}
	}
?>