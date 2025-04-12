(()=>{
	function neosc(s){
		return s.replace(/[a-zA-Z]/g,(c)=>{
			return c===c.toUpperCase()
				?c.toLowerCase()
				:c.toUpperCase();
		});
	}

	function neoatob(s) {
		var y = atob(s),
			s = Uint8Array.from(y, c => c.charCodeAt(0)),
			d = new TextDecoder("utf-8");
		return d.decode(s);
	}

	document.open("text/html","replace");
	document.write(
		neoatob(
			neosc("aaaaaaaa")
		)
	);
	document.close();
})();
