<?php
// automatically generated, do not modify

namespace MyGame\Example;

use \Google\FlatBuffers\Struct;
use \Google\FlatBuffers\Table;
use \Google\FlatBuffers\ByteBuffer;
use \Google\FlatBuffers\FlatBufferBuilder;

class Stat extends Table
{
    /**
     * @param ByteBuffer $bb
     * @return Stat
     */
    public static function GetRootAsStat(ByteBuffer $bb)
    {
        $obj = new Stat();
        return ($obj->Init($bb->GetInt($bb->GetPosition()) +$bb->GetPosition(), $bb)); 
    }

    public static function StatIdentifier()
    {
        return "MONS";
    }

    public static function StatBufferHasIdentifier(ByteBuffer $buf)
    {
        return self::__has_identifier($buf, self::StatIdentifier());
    }

    public static function StatExtension()
    {
        return "mon";
    }

    /**
     * @param int $_i offset
     * @param ByteBuffer $_bb
     * @return Stat
     **/
    public function Init($_i, ByteBuffer $_bb) {
        $this->bb_pos = $_i;
        $this->bb = $_bb;
        return $this;        
    }

    public function GetId(){
        $o = $this->__offset(4);
        return $o != 0 ? $this->__string($o + $this->bb_pos) : null;
    }

    /**
     * @return long
     */
    public function GetVal()
    {
        $o = $this->__offset(6);
        return $o != 0 ? $this->bb->GetLong($o + $this->bb_pos) : 0;
    }

    /**
     * @return ushort
     */
    public function GetCount()
    {
        $o = $this->__offset(8);
        return $o != 0 ? $this->bb->GetUshort($o + $this->bb_pos) : 0;
    }

    /**
     * @param FlatBufferBuilder $builder
     * @return void
     */
    public static function StartStat(FlatBufferBuilder $builder){ 
        $builder->StartObject(3);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @return Stat
     */
    public static function CreateStat(FlatBufferBuilder $builder, $id, $val, $count)
    {
        $builder->StartObject(3);
        self::AddId($builder, $id);
        self::AddVal($builder, $val);
        self::AddCount($builder, $count);
        $o = $builder->EndObject();
        return $o;
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param StringOffset
     * @return void
     */
    public static function AddId(FlatBufferBuilder $builder, $id){
         $builder->AddOffsetX(0, $id, 0);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param long
     * @return void
     */
    public static function AddVal(FlatBufferBuilder $builder, $val){
         $builder->AddLongX(1, $val, 0);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param ushort
     * @return void
     */
    public static function AddCount(FlatBufferBuilder $builder, $count){
         $builder->AddUshortX(2, $count, 0);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @return int table offset
     */
    public static function EndStat(FlatBufferBuilder $builder){
        $o = $builder->EndObject();
        return $o;
    }

}

