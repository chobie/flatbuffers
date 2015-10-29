<?php
// automatically generated, do not modify

namespace MyGame\Example;

use \FlatBuffers\Struct;
use \FlatBuffers\Table;
use \FlatBuffers\ByteBuffer;
use \FlatBuffers\FlatBufferBuilder;

class Vec3 extends Struct
{
    /**
     * @param int $_i offset
     * @param ByteBuffer $_bb
     * @return Vec3
     **/
    public function Init($_i, ByteBuffer $_bb) {
        $this->bb_pos = $_i;
        $this->bb = $_bb;
        return $this;        
    }

    /**
     * @return float
     */
    public function GetX()
    {
        return $this->bb->GetFloat($this->bb_pos + 0);
    }

    /**
     * @return float
     */
    public function GetY()
    {
        return $this->bb->GetFloat($this->bb_pos + 4);
    }

    /**
     * @return float
     */
    public function GetZ()
    {
        return $this->bb->GetFloat($this->bb_pos + 8);
    }

    /**
     * @return double
     */
    public function GetTest1()
    {
        return $this->bb->GetDouble($this->bb_pos + 16);
    }

    /**
     * @return sbyte
     */
    public function GetTest2()
    {
        return $this->bb->GetSbyte($this->bb_pos + 24);
    }

    /**
     * @return Test
     */
    public function GetTest3()
    {
        $obj = new Test();
        $obj->init($this->bb_pos + 26, $this->bb);
        return $obj;
    }


    /**
     * @return int offset
     */
    public static function CreateVec3(FlatBufferBuilder $builder, $x, $y, $z, $test1, $test2, $test3_a, $test3_b){
        $builder->prep(16, 32);
        $builder->pad(2);
        $builder->prep(2, 4);
        $builder->pad(1);
        $builder->PutSbyte($test3_b);
        $builder->PutShort($test3_a);
        $builder->pad(1);
        $builder->PutSbyte($test2);
        $builder->PutDouble($test1);
        $builder->pad(4);
        $builder->PutFloat($z);
        $builder->PutFloat($y);
        $builder->PutFloat($x);
        return $builder->offset();
    }

}

