<?php
// automatically generated, do not modify

namespace MyGame\Example;

use \Google\FlatBuffers\Struct;
use \Google\FlatBuffers\Table;
use \Google\FlatBuffers\ByteBuffer;
use \Google\FlatBuffers\FlatBufferBuilder;

class Test extends Struct
{
    /**
     * @param int $_i offset
     * @param ByteBuffer $_bb
     * @return Test
     **/
    public function Init($_i, ByteBuffer $_bb) {
        $this->bb_pos = $_i;
        $this->bb = $_bb;
        return $this;        
    }

    /**
     * @return short
     */
    public function GetA()
    {
        return $this->bb->GetShort($this->bb_pos + 0);
    }

    /**
     * @return sbyte
     */
    public function GetB()
    {
        return $this->bb->GetSbyte($this->bb_pos + 2);
    }


    /**
     * @return int offset
     */
    public static function CreateTest(FlatBufferBuilder $builder, $a, $b){
        $builder->prep(2, 4);
        $builder->pad(1);
        $builder->PutSbyte($b);
        $builder->PutShort($a);
        return $builder->offset();
    }

}

