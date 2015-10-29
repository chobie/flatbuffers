<?php
namespace FlatBuffers;

abstract class Table
{
    /**
     * @var int $bb_pos
     */
    protected $bb_pos;
    /**
     * @var ByteBuffer $bb
     */
    protected $bb;

    public function __construct()
    {
    }

    /**
     * returns actual vtable offset
     *
     * @param $vtable_offset
     * @return int offset > 0 means exist value. 0 means not exist
     */
    protected function __offset($vtable_offset)
    {
        $vtable = $this->bb_pos - $this->bb->GetInt($this->bb_pos);
        return $vtable_offset < $this->bb->GetShort($vtable) ? $this->bb->GetShort($vtable + $vtable_offset) : 0;
    }

    /**
     * @param $offset
     * @return mixed
     */
    protected function __indirect($offset)
    {
        return $offset + $this->bb->GetInt($offset);
    }

    /**
     * fetch utf8 encoded string.
     *
     * @param $offset
     * @return string
     */
    protected function __string($offset)
    {
        $offset += $this->bb->GetInt($offset);
        $len = $this->bb->GetInt($offset);
        $startPos = $offset + Constants::SIZEOF_INT;
        return substr($this->bb->_buffer, $startPos, $len);
    }

    /**
     * @param $offset
     * @return int
     */
    protected function __vector_len($offset)
    {
        $offset += $this->bb_pos;
        $offset += $this->bb->GetInt($offset);
        return $this->bb->GetInt($offset);
    }

    /**
     * @param $offset
     * @return int
     */
    protected function __vector($offset)
    {
        $offset += $this->bb_pos;
        // data starts after the length
        return $offset + $this->bb->GetInt($offset) + Constants::SIZEOF_INT;
    }

//    protected function __vector_as_bytebuffer($vector_offset, $elem_size)
//    {
//    }

    /**
     * @param Table $table
     * @param int $offset
     * @return Table
     */
    protected function __union($table, $offset)
    {
        $offset += $this->bb_pos;
        $table->bb_pos = $offset + $this->bb->GetInt($offset);
        $table->bb = $this->bb;
        return $table;
    }

    /**
     * @param ByteBuffer $bb
     * @param string $ident
     * @return bool
     * @throws \ArgumentException
     */
    protected static function __has_identifier($bb, $ident)
    {
        if (strlen($ident) != Constants::FILE_IDENTIFIER_LENGTH)
            throw new \ArgumentException("FlatBuffers: file identifier must be length "  . Constants::FILE_IDENTIFIER_LENGTH);

        for ($i = 0; $i < 4; $i++)
        {
            if ($ident[$i] != $bb->Get($bb->GetPosition() + Constants::SIZEOF_INT + $i)) {
                return false;
            }
        }

        return true;
    }
}