<?php
// automatically generated, do not modify

namespace MyGame\Example;

use \FlatBuffers\Struct;
use \FlatBuffers\Table;
use \FlatBuffers\ByteBuffer;
use \FlatBuffers\FlatBufferBuilder;

class TestSimpleTableWithEnum extends Table
{
    /**
     * @param ByteBuffer $bb
     * @return TestSimpleTableWithEnum
     */
    public static function GetRootAsTestSimpleTableWithEnum(ByteBuffer $bb)
    {
        $obj = new TestSimpleTableWithEnum();
        return ($obj->Init($bb->GetInt($bb->GetPosition()) + $bb->GetPosition(), $bb));
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
    public function Init($_i, ByteBuffer $_bb) {
        $this->bb_pos = $_i;
        $this->bb = $_bb;
        return $this;        
    }

    /**
     * @return sbyte
     */
    public function GetColor()
    {
        $o = $this->__offset(4);
        return $o != 0 ? $this->bb->GetSbyte($o + $this->bb_pos) : \MyGame\Example\Color::Green;
    }

    /**
     * @param FlatBufferBuilder $builder
     * @return void
     */
    public static function StartTestSimpleTableWithEnum(FlatBufferBuilder $builder){ 
        $builder->StartObject(1);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @return TestSimpleTableWithEnum
     */
    public static function CreateTestSimpleTableWithEnum(FlatBufferBuilder $builder, $color)
    {
        $builder->StartObject(1);
        self::AddColor($builder, $color);
        $o = $builder->EndObject();
        return $o;
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param sbyte
     * @return void
     */
    public static function AddColor(FlatBufferBuilder $builder, $color){
         $builder->AddSbyteX(0, $color, 2);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @return int table offset
     */
    public static function EndTestSimpleTableWithEnum(FlatBufferBuilder $builder){
        $o = $builder->EndObject();
        return $o;
    }

}

