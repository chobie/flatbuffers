<?php
// automatically generated, do not modify

namespace MyGame\Example;

use \Google\FlatBuffers\Struct;
use \Google\FlatBuffers\Table;
use \Google\FlatBuffers\ByteBuffer;
use \Google\FlatBuffers\FlatBufferBuilder;

class TestSimpleTableWithEnum extends Table
{
    /**
     * @param ByteBuffer $bb
     * @return TestSimpleTableWithEnum
     */
    public static function getRootAsTestSimpleTableWithEnum(ByteBuffer $bb)
    {
        $obj = new TestSimpleTableWithEnum();
        return ($obj->init($bb->getInt($bb->getPosition()) + $bb->getPosition(), $bb));
    }

    public static function TestSimpleTableWithEnumIdentifier()
    {
        return "MONS";
    }

    public static function TestSimpleTableWithEnumBufferHasIdentifier(ByteBuffer $buf)
    {
        return self::__has_identifier($buf, self::TestSimpleTableWithEnumIdentifier());
    }

    public static function TestSimpleTableWithEnumExtension()
    {
        return "mon";
    }

    /**
     * @param int $_i offset
     * @param ByteBuffer $_bb
     * @return TestSimpleTableWithEnum
     **/
    public function init($_i, ByteBuffer $_bb)
    {
        $this->bb_pos = $_i;
        $this->bb = $_bb;
        return $this;
    }

    /**
     * @return sbyte
     */
    public function getColor()
    {
        $o = $this->__offset(4);
        return $o != 0 ? $this->bb->getSbyte($o + $this->bb_pos) : \MyGame\Example\Color::Green;
    }

    /**
     * @param FlatBufferBuilder $builder
     * @return void
     */
    public static function startTestSimpleTableWithEnum(FlatBufferBuilder $builder)
    {
        $builder->StartObject(1);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @return TestSimpleTableWithEnum
     */
    public static function createTestSimpleTableWithEnum(FlatBufferBuilder $builder, $color)
    {
        $builder->startObject(1);
        self::addColor($builder, $color);
        $o = $builder->endObject();
        return $o;
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param sbyte
     * @return void
     */
    public static function AddColor(FlatBufferBuilder $builder, $color)
    {
        $builder->addSbyteX(0, $color, 2);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @return int table offset
     */
    public static function endTestSimpleTableWithEnum(FlatBufferBuilder $builder)
    {
        $o = $builder->endObject();
        return $o;
    }
}
