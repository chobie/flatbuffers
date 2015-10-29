<?php
namespace FlatBuffers;

abstract class Struct
{
    /**
     * @var int $bb_pos
     */
    protected $bb_pos;

    /**
     * @var ByteBuffer $bb
     */
    protected $bb;
}
