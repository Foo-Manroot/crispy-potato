
<?php
	/* Biblioteca para el analizador de Markdown */
	include "../lib/Parsedown.php";

	/**
	 * Obtiene el artículo con el id especificado.
	 *
	 * @param id_art
	 *		ID del artículo a obtener.
	 *
	 * @return
	 *		El texto del artículo, si se
	 *	ha encontrado; o un texto avisando del
	 *	error que se haya producido.
	 */
	function obtener_art ($id_art)
	{
		$bd = "";
		$host = "";
		$usuario = "";
		$contr = "";

		$texto = "## No se ha encontrado el artículo especificado";
		$conn = pg_connect ("host=$host dbname=$bd user=$usuario password=$contr");

		if (!$conn)
		{
			return "Error al conectarse a la base de datos.";
		}

		/* Prepara y ejecuta la consulta */
		$consulta = pg_prepare ($conn, "ver_art", "SELECT * FROM articulos WHERE id_articulo = $1");
		$consulta = pg_execute ($conn, "ver_art", array ($id_art));

		/* Si se ha encontrado, se carga el texto */
		if (!$consulta || pg_num_rows ($consulta) != 1)
		{
			$texto =  "## No se ha encontrado el artículo especificado";
		}
		else
		{
			$articulo = pg_fetch_array ($consulta);
			$texto = $articulo["texto"];
		}

		$Parsedown = new Parsedown ();

		$texto = $Parsedown->text ($texto);

		pg_close ($conn);
		return $texto;
	}

	/**
	 * Obtiene la cuenta del usuario especificado.
	 *
	 * @param usuario
	 *		Correo del usuario cuya contraseña se desea obtener.
	 *
	 * @return
	 *		Array con los campos de la tupla resultado (si
	 *	existe) de la tabla 'usuarios': ['email', 'nombre', 'pass'];
	 *	o null si ha habido algún problema.
	 */
	function obtener_cuenta ($email)
	{
		$bd = "";
		$host = "";
		$usuario = "";
		$contr = "";

		$tupla = null;
		$conn = pg_connect ("host=$host dbname=$bd user=$usuario password=$contr");

		if (!$conn)
		{
			return null;
		}

		/* Prepara y ejecuta la consulta */
		$consulta = pg_prepare ($conn, "ver_pass", "SELECT * FROM usuarios WHERE email = $1");
		$consulta = pg_execute ($conn, "ver_pass", array ($email));

		/* Si se ha encontrado, se carga el texto */
		if ($consulta && pg_num_rows ($consulta) == 1)
		{
			$tupla = pg_fetch_array ($consulta);
		}

		pg_close ($conn);
		return $tupla;
	}
?>