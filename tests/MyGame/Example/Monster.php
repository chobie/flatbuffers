<?php
// automatically generated, do not modify

namespace MyGame\Example;

use \FlatBuffers\Struct;
use \FlatBuffers\Table;
use \FlatBuffers\ByteBuffer;
use \FlatBuffers\FlatBufferBuilder;

/// an example documentation comment: monster object
class Monster extends Table
{
    /**
     * @param ByteBuffer $bb
     * @return Monster
     */
    public static function GetRootAsMonster(ByteBuffer $bb)
    {
        $obj = new Monster();
        return ($obj->Init($bb->GetInt($bb->GetPosition()) + $bb->GetPosition(), $bb));
    }

    public static function MonsterIdentifier()
    {
        return "MONS";
    }

    public static function MonsterBufferHasIdentifier(ByteBuffer $buf)
    {
        return self::__has_identifier($buf, self::MonsterIdentifier());
    }

    public static function MonsterExtension()
    {
        return "mon";
    }

    /**
     * @param int $_i offset
     * @param ByteBuffer $_bb
     * @return Monster
     **/
    public function Init($_i, ByteBuffer $_bb) {
        $this->bb_pos = $_i;
        $this->bb = $_bb;
        return $this;        
    }

    public function GetPos(){
        $obj = new Vec3();
        $o = $this->__offset(4);
        return $o != 0 ? $obj->init($o + $this->bb_pos, $this->bb) : 0;
    }

    /**
     * @return short
     */
    public function GetMana()
    {
        $o = $this->__offset(6);
        return $o != 0 ? $this->bb->GetShort($o + $this->bb_pos) : 150;
    }

    /**
     * @return short
     */
    public function GetHp()
    {
        $o = $this->__offset(8);
        return $o != 0 ? $this->bb->GetShort($o + $this->bb_pos) : 100;
    }

    public function GetName(){
        $o = $this->__offset(10);
        return $o != 0 ? $this->__string($o + $this->bb_pos) : null;
    }

    /**
     * @param int offset
     * @return byte
     */
    public function GetInventory($j) {
        $o = $this->__offset(14);
        return $o != 0 ? $this->bb->GetByte($this->__vector($o) + $j * 1) : 0;
    }

    /**
     * @return int
     */
    public function GetInventoryLength()
{
        $o = $this->__offset(14);
        return $o != 0 ? $this->__vector_len($o) : 0;
    }

    /**
     * @return sbyte
     */
    public function GetColor()
    {
        $o = $this->__offset(16);
        return $o != 0 ? $this->bb->GetSbyte($o + $this->bb_pos) : \MyGame\Example\Color::Blue;
    }

    /**
     * @return byte
     */
    public function GetTestType()
    {
        $o = $this->__offset(18);
        return $o != 0 ? $this->bb->GetByte($o + $this->bb_pos) : \MyGame\Example\Any::NONE;
    }

    /**
     * @returnint
     */
    public function GetTest($obj)
    {
        $o = $this->__offset(20);
        return $o != 0 ? $this->__union($obj, $o) : null;
    }

    /**
     * @returnVectorOffset
     */
    public function GetTest4($j) {
        $o = $this->__offset(22);
        $obj = new Test();
// base_type_vector
        return $o != 0 ? $obj->Init($this->__vector($o) + $j *4, $this->bb) : null;
    }

    /**
     * @return int
     */
    public function GetTest4Length()
{
        $o = $this->__offset(22);
        return $o != 0 ? $this->__vector_len($o) : 0;
    }

    /**
     * @param int offset
     * @return string
     */
    public function GetTestarrayofstring($j) {
        $o = $this->__offset(24);
        return $o != 0 ? $this->__string($this->__vector($o) + $j * 4) : 0;
    }

    /**
     * @return int
     */
    public function GetTestarrayofstringLength()
{
        $o = $this->__offset(24);
        return $o != 0 ? $this->__vector_len($o) : 0;
    }

/// an example documentation comment: this will end up in the generated code
/// multiline too
    /**
     * @returnVectorOffset
     */
    public function GetTestarrayoftables($j) {
        $o = $this->__offset(26);
        $obj = new Monster();
// base_type_vector
        return $o != 0 ? $obj->Init($this->__indirect($this->__vector($o) + $j * 4), $this->bb) : null;
    }

    /**
     * @return int
     */
    public function GetTestarrayoftablesLength()
{
        $o = $this->__offset(26);
        return $o != 0 ? $this->__vector_len($o) : 0;
    }

    public function GetEnemy(){
        $obj = new Monster();
        $o = $this->__offset(28);
        return $o != 0 ? $obj->init($o + $this->bb_pos, $this->bb) : 0;
    }

    /**
     * @param int offset
     * @return byte
     */
    public function GetTestnestedflatbuffer($j) {
        $o = $this->__offset(30);
        return $o != 0 ? $this->bb->GetByte($this->__vector($o) + $j * 1) : 0;
    }

    /**
     * @return int
     */
    public function GetTestnestedflatbufferLength()
{
        $o = $this->__offset(30);
        return $o != 0 ? $this->__vector_len($o) : 0;
    }

    public function GetTestempty(){
        $obj = new Stat();
        $o = $this->__offset(32);
        return $o != 0 ? $obj->init($o + $this->bb_pos, $this->bb) : 0;
    }

    /**
     * @return bool
     */
    public function GetTestbool()
    {
        $o = $this->__offset(34);
        return $o != 0 ? $this->bb->GetBool($o + $this->bb_pos) : false;
    }

    /**
     * @return int
     */
    public function GetTesthashs32Fnv1()
    {
        $o = $this->__offset(36);
        return $o != 0 ? $this->bb->GetInt($o + $this->bb_pos) : 0;
    }

    /**
     * @return uint
     */
    public function GetTesthashu32Fnv1()
    {
        $o = $this->__offset(38);
        return $o != 0 ? $this->bb->GetUint($o + $this->bb_pos) : 0;
    }

    /**
     * @return long
     */
    public function GetTesthashs64Fnv1()
    {
        $o = $this->__offset(40);
        return $o != 0 ? $this->bb->GetLong($o + $this->bb_pos) : 0;
    }

    /**
     * @return ulong
     */
    public function GetTesthashu64Fnv1()
    {
        $o = $this->__offset(42);
        return $o != 0 ? $this->bb->GetUlong($o + $this->bb_pos) : 0;
    }

    /**
     * @return int
     */
    public function GetTesthashs32Fnv1a()
    {
        $o = $this->__offset(44);
        return $o != 0 ? $this->bb->GetInt($o + $this->bb_pos) : 0;
    }

    /**
     * @return uint
     */
    public function GetTesthashu32Fnv1a()
    {
        $o = $this->__offset(46);
        return $o != 0 ? $this->bb->GetUint($o + $this->bb_pos) : 0;
    }

    /**
     * @return long
     */
    public function GetTesthashs64Fnv1a()
    {
        $o = $this->__offset(48);
        return $o != 0 ? $this->bb->GetLong($o + $this->bb_pos) : 0;
    }

    /**
     * @return ulong
     */
    public function GetTesthashu64Fnv1a()
    {
        $o = $this->__offset(50);
        return $o != 0 ? $this->bb->GetUlong($o + $this->bb_pos) : 0;
    }

    /**
     * @param int offset
     * @return bool
     */
    public function GetTestarrayofbools($j) {
        $o = $this->__offset(52);
        return $o != 0 ? $this->bb->GetBool($this->__vector($o) + $j * 1) : 0;
    }

    /**
     * @return int
     */
    public function GetTestarrayofboolsLength()
{
        $o = $this->__offset(52);
        return $o != 0 ? $this->__vector_len($o) : 0;
    }

    /**
     * @param FlatBufferBuilder $builder
     * @return void
     */
    public static function StartMonster(FlatBufferBuilder $builder){ 
        $builder->StartObject(25);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @return Monster
     */
    public static function CreateMonster(FlatBufferBuilder $builder, $pos, $mana, $hp, $name, $inventory, $color, $test_type, $test, $test4, $testarrayofstring, $testarrayoftables, $enemy, $testnestedflatbuffer, $testempty, $testbool, $testhashs32_fnv1, $testhashu32_fnv1, $testhashs64_fnv1, $testhashu64_fnv1, $testhashs32_fnv1a, $testhashu32_fnv1a, $testhashs64_fnv1a, $testhashu64_fnv1a, $testarrayofbools)
    {
        $builder->StartObject(25);
        self::AddPos($builder, $pos);
        self::AddMana($builder, $mana);
        self::AddHp($builder, $hp);
        self::AddName($builder, $name);
        self::AddInventory($builder, $inventory);
        self::AddColor($builder, $color);
        self::AddTestType($builder, $test_type);
        self::AddTest($builder, $test);
        self::AddTest4($builder, $test4);
        self::AddTestarrayofstring($builder, $testarrayofstring);
        self::AddTestarrayoftables($builder, $testarrayoftables);
        self::AddEnemy($builder, $enemy);
        self::AddTestnestedflatbuffer($builder, $testnestedflatbuffer);
        self::AddTestempty($builder, $testempty);
        self::AddTestbool($builder, $testbool);
        self::AddTesthashs32Fnv1($builder, $testhashs32_fnv1);
        self::AddTesthashu32Fnv1($builder, $testhashu32_fnv1);
        self::AddTesthashs64Fnv1($builder, $testhashs64_fnv1);
        self::AddTesthashu64Fnv1($builder, $testhashu64_fnv1);
        self::AddTesthashs32Fnv1a($builder, $testhashs32_fnv1a);
        self::AddTesthashu32Fnv1a($builder, $testhashu32_fnv1a);
        self::AddTesthashs64Fnv1a($builder, $testhashs64_fnv1a);
        self::AddTesthashu64Fnv1a($builder, $testhashu64_fnv1a);
        self::AddTestarrayofbools($builder, $testarrayofbools);
        $o = $builder->EndObject();
        $builder->required($o, 10);  // name
        return $o;
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param int
     * @return void
     */
    public static function AddPos(FlatBufferBuilder $builder, $pos){
         $builder->AddStructX(0, $pos, 0);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param short
     * @return void
     */
    public static function AddMana(FlatBufferBuilder $builder, $mana){
         $builder->AddShortX(1, $mana, 150);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param short
     * @return void
     */
    public static function AddHp(FlatBufferBuilder $builder, $hp){
         $builder->AddShortX(2, $hp, 100);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param StringOffset
     * @return void
     */
    public static function AddName(FlatBufferBuilder $builder, $name){
         $builder->AddOffsetX(3, $name, 0);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param VectorOffset
     * @return void
     */
    public static function AddInventory(FlatBufferBuilder $builder, $inventory){
         $builder->AddOffsetX(5, $inventory, 0);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param array offset array
     * @return int vector offset
     */
    public static function CreateInventoryVector(FlatBufferBuilder $builder, array $data){
        $builder->StartVector(1, count($data), 1);
        for ($i = count($data) - 1; $i >= 0; $i--) {
            $builder->AddByte($data[$i]);
        }
        return $builder->EndVector();
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param int $numElems
     * @return void
     */
    public static function StartInventoryVector(FlatBufferBuilder $builder, $numElems){
        $builder->StartVector(1, $numElems, 1);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param sbyte
     * @return void
     */
    public static function AddColor(FlatBufferBuilder $builder, $color){
         $builder->AddSbyteX(6, $color, 8);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param byte
     * @return void
     */
    public static function AddTestType(FlatBufferBuilder $builder, $testType){
         $builder->AddByteX(7, $testType, 0);
    }

    public static function AddTest(FlatBufferBuilder $builder, $offset) {
        $builder->AddOffsetX(8, $offset, 0);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param VectorOffset
     * @return void
     */
    public static function AddTest4(FlatBufferBuilder $builder, $test4){
         $builder->AddOffsetX(9, $test4, 0);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param array offset array
     * @return int vector offset
     */
    public static function CreateTest4Vector(FlatBufferBuilder $builder, array $data){
        $builder->StartVector(4, count($data), 2);
        for ($i = count($data) - 1; $i >= 0; $i--) {
            $builder->AddOffset($data[$i]);
        }
        return $builder->EndVector();
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param int $numElems
     * @return void
     */
    public static function StartTest4Vector(FlatBufferBuilder $builder, $numElems){
        $builder->StartVector(4, $numElems, 2);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param VectorOffset
     * @return void
     */
    public static function AddTestarrayofstring(FlatBufferBuilder $builder, $testarrayofstring){
         $builder->AddOffsetX(10, $testarrayofstring, 0);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param array offset array
     * @return int vector offset
     */
    public static function CreateTestarrayofstringVector(FlatBufferBuilder $builder, array $data){
        $builder->StartVector(4, count($data), 4);
        for ($i = count($data) - 1; $i >= 0; $i--) {
            $builder->AddOffset($data[$i]);
        }
        return $builder->EndVector();
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param int $numElems
     * @return void
     */
    public static function StartTestarrayofstringVector(FlatBufferBuilder $builder, $numElems){
        $builder->StartVector(4, $numElems, 4);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param VectorOffset
     * @return void
     */
    public static function AddTestarrayoftables(FlatBufferBuilder $builder, $testarrayoftables){
         $builder->AddOffsetX(11, $testarrayoftables, 0);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param array offset array
     * @return int vector offset
     */
    public static function CreateTestarrayoftablesVector(FlatBufferBuilder $builder, array $data){
        $builder->StartVector(4, count($data), 4);
        for ($i = count($data) - 1; $i >= 0; $i--) {
            $builder->AddOffset($data[$i]);
        }
        return $builder->EndVector();
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param int $numElems
     * @return void
     */
    public static function StartTestarrayoftablesVector(FlatBufferBuilder $builder, $numElems){
        $builder->StartVector(4, $numElems, 4);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param int
     * @return void
     */
    public static function AddEnemy(FlatBufferBuilder $builder, $enemy){
         $builder->AddOffsetX(12, $enemy, 0);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param VectorOffset
     * @return void
     */
    public static function AddTestnestedflatbuffer(FlatBufferBuilder $builder, $testnestedflatbuffer){
         $builder->AddOffsetX(13, $testnestedflatbuffer, 0);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param array offset array
     * @return int vector offset
     */
    public static function CreateTestnestedflatbufferVector(FlatBufferBuilder $builder, array $data){
        $builder->StartVector(1, count($data), 1);
        for ($i = count($data) - 1; $i >= 0; $i--) {
            $builder->AddByte($data[$i]);
        }
        return $builder->EndVector();
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param int $numElems
     * @return void
     */
    public static function StartTestnestedflatbufferVector(FlatBufferBuilder $builder, $numElems){
        $builder->StartVector(1, $numElems, 1);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param int
     * @return void
     */
    public static function AddTestempty(FlatBufferBuilder $builder, $testempty){
         $builder->AddOffsetX(14, $testempty, 0);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param bool
     * @return void
     */
    public static function AddTestbool(FlatBufferBuilder $builder, $testbool){
         $builder->AddBoolX(15, $testbool, false);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param int
     * @return void
     */
    public static function AddTesthashs32Fnv1(FlatBufferBuilder $builder, $testhashs32Fnv1){
         $builder->AddIntX(16, $testhashs32Fnv1, 0);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param uint
     * @return void
     */
    public static function AddTesthashu32Fnv1(FlatBufferBuilder $builder, $testhashu32Fnv1){
         $builder->AddUintX(17, $testhashu32Fnv1, 0);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param long
     * @return void
     */
    public static function AddTesthashs64Fnv1(FlatBufferBuilder $builder, $testhashs64Fnv1){
         $builder->AddLongX(18, $testhashs64Fnv1, 0);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param ulong
     * @return void
     */
    public static function AddTesthashu64Fnv1(FlatBufferBuilder $builder, $testhashu64Fnv1){
         $builder->AddUlongX(19, $testhashu64Fnv1, 0);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param int
     * @return void
     */
    public static function AddTesthashs32Fnv1a(FlatBufferBuilder $builder, $testhashs32Fnv1a){
         $builder->AddIntX(20, $testhashs32Fnv1a, 0);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param uint
     * @return void
     */
    public static function AddTesthashu32Fnv1a(FlatBufferBuilder $builder, $testhashu32Fnv1a){
         $builder->AddUintX(21, $testhashu32Fnv1a, 0);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param long
     * @return void
     */
    public static function AddTesthashs64Fnv1a(FlatBufferBuilder $builder, $testhashs64Fnv1a){
         $builder->AddLongX(22, $testhashs64Fnv1a, 0);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param ulong
     * @return void
     */
    public static function AddTesthashu64Fnv1a(FlatBufferBuilder $builder, $testhashu64Fnv1a){
         $builder->AddUlongX(23, $testhashu64Fnv1a, 0);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param VectorOffset
     * @return void
     */
    public static function AddTestarrayofbools(FlatBufferBuilder $builder, $testarrayofbools){
         $builder->AddOffsetX(24, $testarrayofbools, 0);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param array offset array
     * @return int vector offset
     */
    public static function CreateTestarrayofboolsVector(FlatBufferBuilder $builder, array $data){
        $builder->StartVector(1, count($data), 1);
        for ($i = count($data) - 1; $i >= 0; $i--) {
            $builder->AddBool($data[$i]);
        }
        return $builder->EndVector();
    }

    /**
     * @param FlatBufferBuilder $builder
     * @param int $numElems
     * @return void
     */
    public static function StartTestarrayofboolsVector(FlatBufferBuilder $builder, $numElems){
        $builder->StartVector(1, $numElems, 1);
    }

    /**
     * @param FlatBufferBuilder $builder
     * @return int table offset
     */
    public static function EndMonster(FlatBufferBuilder $builder){
        $o = $builder->EndObject();
        $builder->required($o, 10);  // name
        return $o;
    }

    public static function FinishMonsterBuffer(FlatBufferBuilder $builder, $offset)
    {
        $builder->finish($offset, "MONS");
    }

}

