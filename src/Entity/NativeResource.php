<?php

/**
 * @package Nuance
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Nuance\Entity;

use DecodeLabs\Coercion;

class NativeResource implements Value
{
    /**
     * @var resource
     */
    public mixed $value;

    public int $id {
        get {
            $parts = explode('#', (string)$this->value, 2);
            return Coercion::toInt(array_pop($parts));
        }
    }

    public string $type {
        get {
            return get_resource_type($this->value);
        }
    }

    /**
     * @param resource $value
     */
    public function __construct(
        mixed $value
    ) {
        $this->value = $value;
    }


    /**
     * @return ?array<string,mixed>
     */
    public function getMetaValues(): ?array
    {
        return match($this->type) {
            // Bzip
            'bzip2' => null,

            // Cubrid
            'cubrid connection',
            'persistent cubrid connection',
            'cubrid request',
            'cubrid lob',
            'cubrid lob2' => null,

            // Dba
            'dba',
            'dba persistent' => $this->getDbaMetaValues(),

            // Dbase
            'dbase' => null,

            // Firebird
            'fbsql link' => null,
            'fbsql plink' => null,
            'fbsql result' => null,

            // FDF
            'fdf' => null,

            // Interbase
            'interbase blob',
            'interbase link',
            'interbase link persistent',
            'interbase query',
            'interbase result' => null,

            // Mailparse
            'mailparse_mail_structure' => null,

            // Oci8
            'oci8 collection',
            'oci8 connection',
            'oci8 lob',
            'oci8 statement' => null,

            // Odbc
            'odbc link',
            'odbc link persistent',
            'odbc result' => null,

            // OpenSSL
            'OpenSSL key',
            'OpenSSL X.509' => null,

            // PDF
            'pdf document',
            'pdf image',
            'pdf object',
            'pdf outline' => null,

            // Process
            'process' => $this->getProcessMetaValues(),

            // Stream
            'stream' => $this->getStreamMetaValues(),

            // Socket
            'Socket',
            'socket' => null,


            // SSH
            'SSH2 Session',
            'SSH2 Listener',
            'SSH2 SFTP',
            'SSH2 Publickey Subsystem' => null,

            // Sybase
            'sybase-db link',
            'sybase-db link persistent',
            'sybase-db result',
            'sybase-ct link',
            'sybase-ct link persistent',
            'sybase-ct result' => null,


            // Wddx
            'wddx' => null,

            default => null,
        };
    }

    /**
     * @return array<string,mixed>
     */
    private function getDbaMetaValues(): array
    {
        $list = dba_list();

        return [
            'file' => $list[(int)$this->value] ?? null,
        ];
    }

    /**
     * @return array<string,mixed>
     */
    private function getProcessMetaValues(): array
    {
        return (array)proc_get_status($this->value);
    }

    /**
     * @return array<string,mixed>
     */
    private function getStreamMetaValues(): array
    {
        return array_merge(
            stream_get_meta_data($this->value),
            stream_context_get_params($this->value)
        );
    }
}
