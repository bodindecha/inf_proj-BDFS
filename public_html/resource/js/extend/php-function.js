const crc32 = (function() {
	const crc32table = (function() {
		var c, crcTable = [];
		for (let n = 0; n < 256; n++) {
			c = n;
			for(let k = 0; k < 8; k++) c = (c & 1 ? (0xEDB88320 ^ (c >>> 1)) : (c >>> 1));
			crcTable[n] = c;
		} return crcTable;
	}());
	return function(string) {
		string = string.toString().trim(); var crc = 0 ^ -1;
		for (let i = 0; i < string.length; i++) crc = (crc >>> 8) ^ crc32table[(crc ^ string.charCodeAt(i)) & 0xFF];
		return (crc ^ -1) >>> 0;
	};
}());

const sha256 = function(string) {
	function rightRotate(value, amount) { return (value >>> amount) | (value << (32 - amount)); };
	var ascii = string.toString().trim(), mathPow = Math.pow, lengthProperty = 'length', i, j, result = '', words = [], hash = sha256.h = sha256.h || [], k = sha256.k = sha256.k || [], isComposite = {};
	var maxWord = mathPow(2, 32), asciiBitLength = ascii[lengthProperty] * 8, primeCounter = k[lengthProperty];
	for (let candidate = 2; primeCounter < 64; candidate++) { if (!isComposite[candidate]) {
        for (i = 0; i < 313; i += candidate) isComposite[i] = candidate;
        hash[primeCounter] = (mathPow(candidate, 0.5) * maxWord) | 0, k[primeCounter++] = (mathPow(candidate, 1/3) * maxWord) | 0;
	} } ascii += '\x80';
	while (ascii[lengthProperty] % 64 - 56) ascii += '\x00';
	for (i = 0; i < ascii[lengthProperty]; i++) {
		j = ascii.charCodeAt(i);
		if (j >> 8) return;
		words[i >> 2] |= j << ((3 - i) % 4) * 8;
	}
    words[words[lengthProperty]] = (asciiBitLength / maxWord) | 0;
	words[words[lengthProperty]] = asciiBitLength
	for (j = 0; j < words[lengthProperty]; ) {
		var w = words.slice(j, j += 16), oldHash = hash;
		hash = hash.slice(0, 8);
		for (i = 0; i < 64; i++) {
			var w15 = w[i - 15], w2 = w[i - 2], a = hash[0], e = hash[4];
			let temp1 = hash[7] + (rightRotate(e, 6) ^ rightRotate(e, 11) ^ rightRotate(e, 25)) + ((e & hash[5]) ^ (~e & hash[6])) + k[i] + (w[i] = i < 16 ? w[i] : (w[i - 16] + (rightRotate(w15, 7) ^ rightRotate(w15, 18) ^ (w15 >>> 3)) + w[i - 7] + (rightRotate(w2, 17) ^ rightRotate(w2, 19) ^ (w2 >>> 10))) | 0);
			hash = [(temp1 + (rightRotate(a, 2) ^ rightRotate(a, 13) ^ rightRotate(a, 22)) + ((a & hash[1]) ^ (a & hash[2]) ^ (hash[1] & hash[2]))) | 0].concat(hash);
			hash[4] = (hash[4] + temp1) | 0;
		} for (i = 0; i < 8; i++) hash[i] = (hash[i] + oldHash[i]) | 0;
	} for (i = 0; i < 8; i++) { for (j = 3; j + 1; j--) {
			let b = (hash[i] >> (j * 8)) & 255;
			result += (b < 16 ? 0 : '') + b.toString(16);
	} } return result;
};