<?php
/** Declaraciones minimas de WP-CLI solo para analisis estatico. */

if ( ! class_exists( 'WP_CLI' ) ) {
	/** Stub de la interfaz estatica utilizada por el plugin. */
	class WP_CLI {
		/** @param string $name Nombre del comando. @param string $class Clase manejadora. */
		public static function add_command( string $name, string $class ): void {}
		/** @param string $message Mensaje. */
		public static function warning( string $message ): void {}
		/** @param string $message Mensaje. */
		public static function error( string $message ): void {}
		/** @param string $message Mensaje. */
		public static function success( string $message ): void {}
	}
}
