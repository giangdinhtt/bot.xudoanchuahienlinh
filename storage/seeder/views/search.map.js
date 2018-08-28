function (doc) {
    if (doc.object_type == undefined) return;
    if (doc.object_type != 'teacher' && doc.object_type != 'student') return;
    if (doc.full_name == undefined) return;

    var mapValue = [doc.grade ? doc.grade : 'none', doc.course ? doc.course : 'none', doc.id, doc._id];

    if (doc.code) emit(doc.code, mapValue);
    if (doc.phone) emit(doc.phone, mapValue);
    if (doc.email) {
        emit(doc.email, 1);
        var emailParts = doc.email.split('@');
        if (emailParts.length > 1) emit(emailParts[0], 1);
    }
    emit(doc.id, mapValue);

    /*
     * Map generated from ftp.unicode.org/Public/UNIDATA/UnicodeData.txt. Maps each upper-case basic alphabet character in
     * the A-Z range to a regex character class that contains all variations (accented, unaccented, upper-case, lower-case)
     * of this character (e.g. A->[AaÄäå....]).
     */
    var charToAccentedIgnoreCaseCharClassMap = {
        'A': '[Aa\u00C0\u00E0\u00C1\u00E1\u1EA0\u1EA1\u1EA2\u1EA3\u00C3\u00E3\u00C2\u00E2\u1EA6\u1EA7\u1EA4\u1EA5\u1EAC\u1EAD\u1EA8\u1EA9\u1EAA\u1EAB\u0102\u0103\u1EB0\u1EB1\u1EB0\u1EAF\u1EB6\u1EB7\u1EB2\u1EB3\u1EB4\u1EB5]',
        'B': '[Bb]',
        'C': '[Cc]',
        'D': '[Dd\u0110\u0111]',
        'E': '[Ee\u00C8\u00E8\u00C9\u00E9\u1EB8\u1EB9\u1EBA\u1EBB\u1EBC\u1EBD\u00CA\u00EA\u1EC0\u1EC1\u1EBE\u1EBF\u1EC6\u1EC7\u1EC2\u1EC3\u1EC4\u1EC5]',
        'F': '[Ff]',
        'G': '[Gg]',
        'H': '[Hh]',
        'I': '[Ii\u00CC\u00EC\u00CD\u00ED\u1ECA\u1ECB\u1EC8\u1EC9\u0128\u0129]',
        'J': '[Jj]',
        'K': '[Kk]',
        'L': '[Ll]',
        'M': '[Mm]',
        'N': '[Nn]',
        'O': '[Oo\u00D2\u00F2\u00D3\u00F3\u1ECC\u1ECD\u1ECE\u1ECF\u00D5\u00F5\u00D4\u00F4\u1ED2\u1ED3\u1ED0\u1ED1\u1ED8\u1ED9\u1ED4\u1ED5\u1ED6\u1ED7\u01A0\u01A1\u1EDC\u1EDD\u1EDA\u1EDB\u1EE2\u1EE3\u1EDE\u1EDF\u1EE0\u1EE1]',
        'P': '[Pp]',
        'Q': '[Qq]',
        'R': '[Rr]',
        'S': '[Ss]',
        'T': '[Tt]',
        'U': '[Uu\u00D9\u00F9\u00DA\u00FA\u1EE4\u1EE5\u1EE6\u1EE7\u0168\u0169\u01AF\u01B0\u1EEA\u1EEB\u1EE8\u1EE9\u1EF0\u1EF1\u1EEC\u1EED\u1EEE\u1EEF]',
        'V': '[Vv]',
        'W': '[Ww]',
        'X': '[Xx]',
        'Y': '[Yy\u1EF2\u1EF3\u00DD\u00FD\u1EF4\u1EF5\u1EF6\u1EF7\u1EF8\u1EF9]',
        'Z': '[Zz]'
    };

    var charToAccentedCharClassMap = {
        'A': '[A\u00C0\u00C1\u1EA0\u1EA2\u00C3\u00C2\u1EA6\u1EA4\u1EAC\u1EA8\u1EAA\u0102\u1EB0\u1EB0\u1EB6\u1EB2\u1EB4]',
        'a': '[a\u00E0\u00E1\u1EA1\u1EA3\u00E3\u00E2\u1EA7\u1EA5\u1EAD\u1EA9\u1EAB\u0103\u1EB1\u1EAF\u1EB7\u1EB3\u1EB5]',
        'B': '[B]',
        'b': '[b]',
        'C': '[C]',
        'c': '[c]',
        'D': '[D\u0110]',
        'd': '[d\u0111]',
        'E': '[E\u00C8\u00C9\u1EB8\u1EBA\u1EBC\u00CA\u1EC0\u1EBE\u1EC6\u1EC2\u1EC4]',
        'e': '[e\u00E8\u00E9\u1EB9\u1EBB\u1EBD\u00EA\u1EC1\u1EBF\u1EC7\u1EC3\u1EC5]',
        'F': '[F]',
        'f': '[f]',
        'G': '[G]',
        'g': '[g]',
        'H': '[Hh]',
        'h': '[h]',
        'I': '[I\u00CC\u00CD\u1ECA\u1EC8\u0128]',
        'i': '[i\u00EC\u00ED\u1ECB\u1EC9\u0129]',
        'J': '[J]',
        'j': '[j]',
        'K': '[K]',
        'k': '[k]',
        'L': '[L]',
        'l': '[l]',
        'M': '[M]',
        'm': '[m]',
        'N': '[N]',
        'n': '[n]',
        'O': '[O\u00D2\u00D3\u1ECC\u1ECE\u00D5\u00D4\u1ED2\u1ED0\u1ED8\u1ED4\u1ED6\u01A0\u1EDC\u1EDA\u1EE2\u1EDE\u1EE0]',
        'o': '[o\u00F2\u00F3\u1ECD\u1ECF\u00F5\u00F4\u1ED3\u1ED1\u1ED9\u1ED5\u1ED7\u01A1\u1EDD\u1EDB\u1EE3\u1EDF\u1EE1]',
        'P': '[P]',
        'p': '[p]',
        'Q': '[Q]',
        'q': '[q]',
        'R': '[R]',
        'r': '[r]',
        'S': '[S]',
        's': '[s]',
        'T': '[T]',
        't': '[t]',
        'U': '[U\u00D9\u00DA\u1EE4\u1EE6\u0168\u01AF\u1EEA\u1EE8\u1EF0\u1EEC\u1EEE]',
        'u': '[u\u00F9\u00FA\u1EE5\u1EE7\u0169\u01B0\u1EEB\u1EE9\u1EF1\u1EED\u1EEF]',
        'V': '[V]',
        'v': '[v]',
        'W': '[W]',
        'w': '[w]',
        'X': '[X]',
        'x': '[x]',
        'Y': '[Y\u1EF2\u00DD\u1EF4\u1EF6\u1EF8]',
        'y': '[y\u1EF3\u00FD\u1EF5\u1EF7\u1EF9]',
        'Z': '[Z]',
        'z': '[z]'
    };

    var standardize = function (str) {
        return str.replace(/\s+/g, ' ');
    }
    /*
     * Returns a string in which each accented and lower-case character from the input is replaced with the respective
     * upper-case base character in the A-Z range (e.g. ä->A, è->E, å->A, ë->E). Hence, the return value for "séléction" is
     * "SELECTION".
     */
    var deaccent = function (str, ignoreCase) {
        var result = str;
        var charClassMap = ignoreCase ? charToAccentedIgnoreCaseCharClassMap : charToAccentedCharClassMap;
        for (var key in charClassMap) {
            result = result.replace(new RegExp(charClassMap[key], ignoreCase ? "gi" : "g"), key);
        }
        return result;
    }

    var fullName = deaccent(standardize(doc.full_name), true).toLowerCase();
    var arr = fullName.split(' ');
    var len = arr.length;
    var keys = [];
    for (i = 0; i < len; i++) {
        var temp = arr[i];
        keys.push(temp);
        for (j = i + 1; j < len; j++) {
            temp += ' ' + arr[j];
            keys.push(temp);
        }
    }
    for (i = 0; i < keys.length; i++) {
        emit(keys[i], mapValue);
    }
}
