/*
* Copyright 2014 Google Inc. All rights reserved.
*
* Licensed under the Apache License, Version 2.0 (the "License");
* you may not use this file except in compliance with the License.
* You may obtain a copy of the License at
*
*     http://www.apache.org/licenses/LICENSE-2.0
*
* Unless required by applicable law or agreed to in writing, software
* distributed under the License is distributed on an "AS IS" BASIS,
* WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
* See the License for the specific language governing permissions and
* limitations under the License.
*/

// independent from idl_parser, since this code is not needed for most clients

#include <string>

#include "flatbuffers/flatbuffers.h"
#include "flatbuffers/idl.h"
#include "flatbuffers/util.h"

namespace flatbuffers {
namespace php {

    static std::string GenGetter(const Type &type);
    static std::string GenDefaultValue(const Value &value);
    static std::string GenMethod(const FieldDef &field);
    static void GenStructBuilder(const StructDef &struct_def,
      std::string *code_ptr);
    static std::string GenTypeBasic(const Type &type);
    static std::string GenTypeGet(const Type &type);
    static std::string TypeName(const FieldDef &field);

    // Ensure that a type is prefixed with its namespace whenever it is used
    // outside of its namespace.
    static std::string WrapInNameSpace(const Namespace *ns,
      const std::string &name) {
      std::string qualified_name = "\\";
      for (auto it = ns->components.begin();
      it != ns->components.end(); ++it) {
        qualified_name += *it + "\\";
      }
      return qualified_name + name;
    }

    static std::string WrapInNameSpace(const Definition &def) {
      return WrapInNameSpace(def.defined_namespace, def.name);
    }


    // Hardcode spaces per indentation.
    const std::string Indent = "  ";

    // Begin by declaring namespace and imports.
    static void BeginFile(const std::string name_space_name,
      const bool needs_imports,
      std::string *code_ptr) {
      std::string &code = *code_ptr;
      code += "<?php\n";
      code += "// automatically generated, do not modify\n\n";
      code += "namespace " + name_space_name + ";\n\n";

      if (needs_imports) {
        code += "use \\FlatBuffers\\Struct;\n";
        code += "use \\FlatBuffers\\Table;\n";
        code += "use \\FlatBuffers\\ByteBuffer;\n";
        code += "use \\FlatBuffers\\FlatBufferBuilder;\n";
        code += "\n";
      }
    }

    // Begin a class declaration.
    static void BeginClass(const StructDef &struct_def, std::string *code_ptr) {
      std::string &code = *code_ptr;
      if (struct_def.fixed) {
        code += "class " + struct_def.name + " extends Struct\n";
      } else {
        code += "class " + struct_def.name + " extends Table\n";
      }
      code += "{\n";
    }

    static void EndClass(std::string *code_ptr) {
      std::string &code = *code_ptr;
      code += "}\n\n";
    }

    // Begin enum code with a class declaration.
    static void BeginEnum(const std::string class_name, std::string *code_ptr) {
      std::string &code = *code_ptr;
      code += "class " + class_name + "\n{\n";
    }

    // A single enum member.
    static void EnumMember(const EnumVal ev, std::string *code_ptr) {
      std::string &code = *code_ptr;
      code += Indent + "const ";
      code += ev.name;
      code += " = ";
      code += NumToString(ev.value) + ";\n";
    }

    // End enum code.
    static void EndEnum(std::string *code_ptr) {
      std::string &code = *code_ptr;
      code += "}\n";
      code += "\n";
    }

    // Initialize a new struct or table from existing data.
    static void NewRootTypeFromBuffer(const StructDef &struct_def,
      std::string *code_ptr) {
      std::string &code = *code_ptr;

      code += Indent + "/**\n";
      code += Indent + " * @param ByteBuffer $bb\n";
      code += Indent + " * @return " + struct_def.name + "\n";
      code += Indent + " */\n";
      code += Indent + "public static function GetRootAs";
      code += struct_def.name;
      code += "(ByteBuffer $bb)\n";
      code += Indent + "{\n";

      code += Indent + Indent + "$obj = new " + struct_def.name + "();\n";
      code += Indent + Indent;
      code += "return ($obj->Init($bb->GetInt($bb->GetPosition())";
      code += " +$bb->GetPosition(), $bb)); \n";
      code += Indent + "}\n\n";
    }

    // Initialize an existing object with other data, to avoid an allocation.
    static void InitializeExisting(const StructDef &struct_def,
      std::string *code_ptr) {
      std::string &code = *code_ptr;

      code += Indent + "/**\n";
      code += Indent + " * @param int $_i offset\n";
      code += Indent + " * @param ByteBuffer $_bb\n";
      code += Indent + " * @return " + struct_def.name + "\n";
      code += Indent + " **/\n";
      code += Indent + "public function Init($_i, ByteBuffer $_bb) {\n";
      code += Indent + Indent + "$this->bb_pos = $_i;\n";
      code += Indent + Indent + "$this->bb = $_bb;\n";
      code += Indent + Indent + "return $this;";
      code += Indent + Indent + "\n";
      code += Indent + "}\n\n";
    }

    // Get the length of a vector.
    static void GetVectorLen(const FieldDef &field,
      std::string *code_ptr) {
      std::string &code = *code_ptr;

      code += Indent + "/**\n";
      code += Indent + " * @return int\n";
      code += Indent + " */\n";
      code += Indent + "public function Get";
      code += MakeCamel(field.name) + "Length()\n";
      code += "{\n";
      code += Indent + Indent + "$o = $this->__offset(";
      code += NumToString(field.value.offset) + ");\n";
      code += Indent + Indent;
      code += "return $o != 0 ? $this->__vector_len($o) : 0;\n";
      code += Indent + "}\n\n";
    }

    // Get the value of a struct's scalar.
    static void GetScalarFieldOfStruct(const FieldDef &field,
      std::string *code_ptr) {
      std::string &code = *code_ptr;
      std::string getter = GenGetter(field.value.type);

      code += Indent + "/**\n";
      code += Indent + " * @return ";
      code += GenTypeGet(field.value.type) + "\n";
      code += Indent + " */\n";
      code += Indent + "public function " + getter;
      code += MakeCamel(field.name) + "()\n";
      code += Indent + "{\n";
      code += Indent + Indent + "return ";

      code += "$this->bb->Get";
      code += MakeCamel(GenTypeGet(field.value.type));
      code += "($this->bb_pos + ";
      code += NumToString(field.value.offset) + ")";
      code += ";\n";

      code += Indent + "}\n\n";
    }

    // Get the value of a table's scalar.
    static void GetScalarFieldOfTable(const FieldDef &field,
      std::string *code_ptr) {
      std::string &code = *code_ptr;
      std::string getter = GenGetter(field.value.type);

      code += Indent + "/**\n";
      code += Indent + " * @return " + GenTypeGet(field.value.type) + "\n";
      code += Indent + " */\n";
      code += Indent + "public function Get";
      code += MakeCamel(field.name);
      code += "()\n";
      code += Indent + "{\n";
      code += Indent + Indent +
        "$o = $this->__offset(" +
        NumToString(field.value.offset) +
        ");\n" + Indent + Indent + "return $o != 0 ? ";
      code += "$this->bb->Get";
      code += MakeCamel(GenTypeGet(field.value.type)) + "($o + $this->bb_pos)";
      code += " : " + GenDefaultValue(field.value) + ";\n";
      code += Indent + "}\n\n";
    }

    // Get a struct by initializing an existing struct.
    // Specific to Struct.
    static void GetStructFieldOfStruct(const FieldDef &field,
      std::string *code_ptr) {
      std::string &code = *code_ptr;

      code += Indent + "/**\n";
      code += Indent + " * @return " + GenTypeGet(field.value.type) + "\n";
      code += Indent + " */\n";
      code += Indent + "public function Get";
      code += MakeCamel(field.name) + "()\n";
      code += Indent + "{\n";
      code += Indent + Indent + "$obj = new ";
      code += GenTypeGet(field.value.type) + "();\n";
      code += Indent + Indent + "$obj->init($this->bb_pos + ";
      code += NumToString(field.value.offset) + ", $this->bb);";
      code += "\n" + Indent + Indent + "return $obj;\n";
      code += Indent + "}\n\n";
    }

    // Get a struct by initializing an existing struct.
    // Specific to Table.
    static void GetStructFieldOfTable(const FieldDef &field,
      std::string *code_ptr) {
      std::string &code = *code_ptr;

      code += Indent + "public function Get";
      code += MakeCamel(field.name);
      code += "(){\n";
      code += Indent + Indent + "$obj = new ";
      code += MakeCamel(GenTypeGet(field.value.type)) + "();\n";
      code += Indent + Indent +
        "$o = $this->__offset(" +
        NumToString(field.value.offset) +
        ");\n";
      code += Indent + Indent;
      code += "return $o != 0 ? $obj->init($o + $this->bb_pos, $this->bb) : ";
      code += GenDefaultValue(field.value) + ";\n";


      code += Indent + "}\n\n";
    }

    // Get the value of a string.
    static void GetStringField(const FieldDef &field,
      std::string *code_ptr) {
      std::string &code = *code_ptr;
      code += Indent + "public function Get";
      code += MakeCamel(field.name);
      code += "(){\n";

      code += Indent + Indent +
        "$o = $this->__offset(" +
        NumToString(field.value.offset) +
        ");\n";
      code += Indent + Indent;
      code += "return $o != 0 ? $this->__string($o + $this->bb_pos) : ";
      code += GenDefaultValue(field.value) + ";\n";
      code += Indent + "}\n\n";
    }

    // Get the value of a union from an object.
    static void GetUnionField(const FieldDef &field,
      std::string *code_ptr) {
      std::string &code = *code_ptr;

      code += Indent + "/**\n";
      code += Indent + " * @return" + GenTypeBasic(field.value.type) + "\n";
      code += Indent + " */\n";
      code += Indent + "public function Get";
      code += MakeCamel(field.name) + "($obj)\n";
      code += Indent + "{\n";
      code += Indent + Indent +
        "$o = $this->__offset(" +
        NumToString(field.value.offset) +
        ");\n";
      code += Indent + Indent;
      code += "return $o != 0 ? $this->__union($obj, $o) : null;\n";
      code += Indent + "}\n\n";
    }

    // Get the value of a vector's struct member.
    static void GetMemberOfVectorOfStruct(const StructDef &struct_def,
      const FieldDef &field,
      std::string *code_ptr) {
      std::string &code = *code_ptr;
      auto vectortype = field.value.type.VectorType();

      code += Indent + "/**\n";
      code += Indent + " * @return" + GenTypeBasic(field.value.type) + "\n";
      code += Indent + " */\n";
      code += Indent + "public function Get";
      code += MakeCamel(field.name);
      code += "($j) {\n";
      code += Indent + Indent +
        "$o = $this->__offset(" +
        NumToString(field.value.offset) +
        ");\n";
      code += Indent + Indent + "$obj = new ";
      code += MakeCamel(GenTypeGet(field.value.type)) + "();\n";

      switch (field.value.type.base_type) {
      case BASE_TYPE_STRUCT:
        if (struct_def.fixed) {
          code += Indent + Indent;
          code += "return $o != 0 ? $obj->init($this->bb_pos +"
            + NumToString(field.value.offset) + ", $this->bb) : null;\n";
        } else {
          code += "//base_type_struct not fixed\n";
          code += Indent + Indent + "return $o != 0 ? $obj->init(";
          code += field.value.type.struct_def->fixed
            ? "$o + $this->bb_pos"
            : "$this->__indirect($o + $this->bb_pos)";
          code += ", $this->bb) : null;\n";
        }
        break;
      case BASE_TYPE_STRING:
        code += "// base_type_string\n";
        // TODO(chobie): do we need this?
        break;
      case BASE_TYPE_VECTOR:
        if (vectortype.base_type == BASE_TYPE_STRUCT) {
          code += Indent + Indent + "return $o != 0 ? $obj->Init(";
          if (vectortype.struct_def->fixed) {
            code += "$this->__vector($o) + $j *";
            code += NumToString(InlineSize(vectortype));
          } else {
            code += "$this->__indirect($this->__vector($o) + $j * ";
            code += NumToString(InlineSize(vectortype)) + ")";
          }
          code += ", $this->bb) : null;\n";
        }
        break;
      case BASE_TYPE_UNION:
        code += Indent + Indent + "return $o != 0 ? $this->";
        code += GenGetter(field.value.type) + "($obj, $o); null;\n";
        break;
      default:
        break;
      }

      code += Indent + "}\n\n";
    }

    // Get the value of a vector's non-struct member. Uses a named return
    // argument to conveniently set the zero value for the result.
    static void GetMemberOfVectorOfNonStruct(const FieldDef &field,
      std::string *code_ptr) {
      std::string &code = *code_ptr;
      auto vectortype = field.value.type.VectorType();

      code += Indent + "/**\n";
      code += Indent + " * @param int offset\n";
      code += Indent + " * @return " + GenTypeGet(field.value.type) + "\n";
      code += Indent + " */\n";
      code += Indent + "public function Get";
      code += MakeCamel(field.name);
      code += "($j) {\n";
      code += Indent + Indent +
        "$o = $this->__offset(" +
        NumToString(field.value.offset) +
        ");\n";

      if (field.value.type.VectorType().base_type == BASE_TYPE_STRING) {
        code += Indent + Indent;
        code += "return $o != 0 ? $this->__string($this->__vector($o) + $j * ";
        code += NumToString(InlineSize(vectortype)) + ") : ";
        code += GenDefaultValue(field.value) + ";\n";
      } else {
        code += Indent + Indent + "return $o != 0 ? $this->bb->Get";
        code += MakeCamel(GenTypeGet(field.value.type));
        code += "($this->__vector($o) + $j * ";
        code += NumToString(InlineSize(vectortype)) + ") : ";
        code += GenDefaultValue(field.value) + ";\n";
      }
      code += Indent + "}\n\n";
    }

    // Recursively generate arguments for a constructor, to deal with nested
    // structs.
    static void StructBuilderArgs(const StructDef &struct_def,
      const char *nameprefix,
      std::string *code_ptr) {
      for (auto it = struct_def.fields.vec.begin();
      it != struct_def.fields.vec.end();
        ++it) {
        auto &field = **it;
        if (IsStruct(field.value.type)) {
          // Generate arguments for a struct inside a struct. To ensure names
          // don't clash, and to make it obvious
          // these arguments are constructing
          // a nested struct, prefix the name with the field name.
          StructBuilderArgs(*field.value.type.struct_def,
            (nameprefix + (field.name + "_")).c_str(),
            code_ptr);
        } else {
          std::string &code = *code_ptr;
          code += (std::string)", $" + nameprefix;
          code += MakeCamel(field.name, false);
        }
      }
    }

    // Recursively generate struct construction statements and instert manual
    // padding.
    static void StructBuilderBody(const StructDef &struct_def,
      const char *nameprefix,
      std::string *code_ptr) {
      std::string &code = *code_ptr;
      code += Indent + Indent + "$builder->prep(";
      code += NumToString(struct_def.minalign) + ", ";
      code += NumToString(struct_def.bytesize) + ");\n";
      for (auto it = struct_def.fields.vec.rbegin();
      it != struct_def.fields.vec.rend();
        ++it) {
        auto &field = **it;
        if (field.padding) {
          code += Indent + Indent + "$builder->pad(";
          code += NumToString(field.padding) + ");\n";
        }
        if (IsStruct(field.value.type)) {
          StructBuilderBody(*field.value.type.struct_def,
            (nameprefix + (field.name + "_")).c_str(),
            code_ptr);
        } else {
          code += Indent + Indent + "$builder->Put" + GenMethod(field) + "($";
          code += nameprefix + MakeCamel(field.name, false) + ");\n";
        }
      }
    }

    // Get the value of a table's starting offset.
    static void GetStartOfTable(const StructDef &struct_def,
      std::string *code_ptr) {
      std::string &code = *code_ptr;

      code += Indent + "/**\n";
      code += Indent + " * @param FlatBufferBuilder $builder\n";
      code += Indent + " * @return void\n";
      code += Indent + " */\n";
      code += Indent + "public static function Start" + struct_def.name;
      code += "(FlatBufferBuilder $builder){ \n";
      code += Indent + Indent + "$builder->StartObject(";
      code += NumToString(struct_def.fields.vec.size());
      code += ");\n";
      code += Indent + "}\n\n";

      code += Indent + "/**\n";
      code += Indent + " * @param FlatBufferBuilder $builder\n";
      code += Indent + " * @return " + struct_def.name + "\n";
      code += Indent + " */\n";
      code += Indent + "public static function Create" + struct_def.name;
      code += "(FlatBufferBuilder $builder, ";

      for (auto it = struct_def.fields.vec.begin();
      it != struct_def.fields.vec.end();
        ++it) {
        auto &field = **it;

        if (field.deprecated) continue;
        code += "$" + field.name;
        if (!(it == (--struct_def.fields.vec.end()))) {
          code += ", ";
        }
      }
      code += ")\n";
      code += Indent + "{\n";
      code += Indent + Indent + "$builder->StartObject(";
      code += NumToString(struct_def.fields.vec.size());
      code += ");\n";
      for (auto it = struct_def.fields.vec.begin();
      it != struct_def.fields.vec.end();
        ++it) {
        auto &field = **it;
        if (field.deprecated) continue;

        code += Indent + Indent + "self::Add";
        code += MakeCamel(field.name) + "($builder, $" + field.name + ");\n";
      }

      code += Indent + Indent + "$o = $builder->EndObject();\n";

      for (auto it = struct_def.fields.vec.begin();
      it != struct_def.fields.vec.end();
        ++it) {
        auto &field = **it;
        if (!field.deprecated && field.required) {
          code += Indent + Indent + "$builder->required($o, ";
          code += NumToString(field.value.offset);
          code += ");  // " + field.name + "\n";
        }
      }
      code += Indent + Indent + "return $o;\n";
      code += Indent + "}\n\n";
    }

    // Set the value of a table's field.
    static void BuildFieldOfTable(const FieldDef &field,
      const size_t offset,
      std::string *code_ptr) {
      std::string &code = *code_ptr;


      code += Indent + "/**\n";
      code += Indent + " * @param FlatBufferBuilder $builder\n";
      code += Indent + " * @param " + GenTypeBasic(field.value.type) + "\n";
      code += Indent + " * @return void\n";
      code += Indent + " */\n";
      code += Indent + "public static function ";
      code += "Add" + MakeCamel(field.name);
      code += "(FlatBufferBuilder $builder, ";
      code += "$" + MakeCamel(field.name, false);
      code += "){\n ";
      code += Indent + Indent + "$builder->Add";
      code += GenMethod(field) + "X(";
      code += NumToString(offset) + ", ";


      code += "$" + MakeCamel(field.name, false);
      code += ", ";

      if (field.value.type.base_type == BASE_TYPE_BOOL) {
        code += "false";
      } else {
        code += field.value.constant;
      }
      code += ");\n";
      code += Indent + "}\n\n";
    }

    // Set the value of one of the members of a table's vector.
    static void BuildVectorOfTable(const FieldDef &field,
      std::string *code_ptr) {
      std::string &code = *code_ptr;

      auto vector_type = field.value.type.VectorType();
      auto alignment = InlineAlignment(vector_type);
      auto elem_size = InlineSize(vector_type);
      code += Indent + "/**\n";
      code += Indent + " * @param FlatBufferBuilder $builder\n";
      code += Indent + " * @param array offset array\n";
      code += Indent + " * @return int vector offset\n";
      code += Indent + " */\n";
      code += Indent + "public static function Create";
      code += MakeCamel(field.name);
      code += "Vector(FlatBufferBuilder $builder, array $data){\n";
      code += Indent + Indent + "$builder->StartVector(";
      code += NumToString(elem_size);
      code += ", count($data), " + NumToString(alignment);
      code += ");\n";
      code += Indent + Indent;
      code += "for ($i = count($data) - 1; $i >= 0; $i--) {\n";
      if (IsScalar(field.value.type.VectorType().base_type)) {
        code += Indent + Indent + Indent;
        code += "$builder->Add";
        code += MakeCamel(GenTypeBasic(field.value.type.VectorType()));
        code += "($data[$i]);\n";
      } else {
        code += Indent + Indent + Indent;
        code += "$builder->AddOffset($data[$i]);\n";
      }
      code += Indent + Indent + "}\n";
      code += Indent + Indent + "return $builder->EndVector();\n";
      code += Indent + "}\n\n";


      code += Indent + "/**\n";
      code += Indent + " * @param FlatBufferBuilder $builder\n";
      code += Indent + " * @param int $numElems\n";
      code += Indent + " * @return void\n";
      code += Indent + " */\n";
      code += Indent + "public static function Start";
      code += MakeCamel(field.name);
      code += "Vector(FlatBufferBuilder $builder, $numElems){\n";
      code += Indent + Indent +  "$builder->StartVector(";
      code += NumToString(elem_size);
      code += ", $numElems, " + NumToString(alignment);
      code += ");\n";
      code += Indent + "}\n\n";
    }

    // Get the offset of the end of a table.
    static void GetEndOffsetOnTable(const Parser &parser,
      const StructDef &struct_def,
      std::string *code_ptr) {
      std::string &code = *code_ptr;


      code += Indent + "/**\n";
      code += Indent + " * @param FlatBufferBuilder $builder\n";
      code += Indent + " * @return int table offset\n";
      code += Indent + " */\n";
      code += Indent + "public static function End" + struct_def.name;
      code += "(FlatBufferBuilder $builder){\n";
      code += Indent + Indent + "$o = $builder->EndObject();\n";


      for (auto it = struct_def.fields.vec.begin();
      it != struct_def.fields.vec.end();
        ++it) {
        auto &field = **it;
        if (!field.deprecated && field.required) {
          code += Indent + Indent + "$builder->required($o, ";
          code += NumToString(field.value.offset);
          code += ");  // " + field.name + "\n";
        }
      }
      code += Indent + Indent + "return $o;\n";
      code += Indent + "}\n\n";

      if (parser.root_struct_def_ == &struct_def) {
        code += Indent + "public static function Finish";
        code += struct_def.name;
        code += "Buffer(FlatBufferBuilder $builder, $offset)\n";
        code += Indent + "{\n";
        code += Indent + Indent + "$builder->finish($offset";

        if (parser.file_identifier_.length())
          code += ", \"" + parser.file_identifier_ + "\"";
        code += ");\n";
        code += Indent + "}\n\n";
      }
    }
    // Generate a struct field, conditioned on its child type(s).
    static void GenStructAccessor(const StructDef &struct_def,
      const FieldDef &field,
      std::string *code_ptr) {
      GenComment(field.doc_comment, code_ptr, nullptr);

      if (IsScalar(field.value.type.base_type)) {
        if (struct_def.fixed) {
          GetScalarFieldOfStruct(field, code_ptr);
        } else {
          GetScalarFieldOfTable(field, code_ptr);
        }
      } else {
        switch (field.value.type.base_type) {
        case BASE_TYPE_STRUCT:
          if (struct_def.fixed) {
            GetStructFieldOfStruct(field, code_ptr);
          } else {
            GetStructFieldOfTable(field, code_ptr);
          }
          break;
        case BASE_TYPE_STRING:
          GetStringField(field, code_ptr);
          break;
        case BASE_TYPE_VECTOR: {
          auto vectortype = field.value.type.VectorType();
          if (vectortype.base_type == BASE_TYPE_STRUCT) {
            GetMemberOfVectorOfStruct(struct_def, field, code_ptr);
          } else {
            GetMemberOfVectorOfNonStruct(field, code_ptr);
          }
          break;
        }
        case BASE_TYPE_UNION:
          GetUnionField(field, code_ptr);
          break;
        default:
          assert(0);
        }
      }
      if (field.value.type.base_type == BASE_TYPE_VECTOR) {
        GetVectorLen(field, code_ptr);
      }
    }

    // Generate table constructors, conditioned on its members' types.
    static void GenTableBuilders(const Parser &parser,
      const StructDef &struct_def,
      std::string *code_ptr) {
      GetStartOfTable(struct_def, code_ptr);

      for (auto it = struct_def.fields.vec.begin();
      it != struct_def.fields.vec.end();
        ++it) {
        auto &field = **it;
        if (field.deprecated) continue;

        auto offset = it - struct_def.fields.vec.begin();
        if (field.value.type.base_type == BASE_TYPE_UNION) {
          std::string &code = *code_ptr;
          code += Indent + "public static function Add";
          code += MakeCamel(field.name);
          code += "(FlatBufferBuilder $builder, $offset) {\n";
          code += Indent + Indent + "$builder->AddOffsetX(";
          code += NumToString(offset) + ", $offset, 0);\n";
          code += Indent + "}\n\n";
        } else {
          BuildFieldOfTable(field, offset, code_ptr);
        }
        if (field.value.type.base_type == BASE_TYPE_VECTOR) {
          BuildVectorOfTable(field, code_ptr);
        }
      }

      GetEndOffsetOnTable(parser, struct_def, code_ptr);
    }

    // Generate struct or table methods.
    static void GenStruct(const Parser &parser, const StructDef &struct_def,
      std::string *code_ptr) {
      if (struct_def.generated) return;

      GenComment(struct_def.doc_comment, code_ptr, nullptr);
      BeginClass(struct_def, code_ptr);

      if (!struct_def.fixed) {
        // Generate a special accessor for the table that has been declared as
        // the root type.
        NewRootTypeFromBuffer(struct_def, code_ptr);
      }

      std::string &code = *code_ptr;
      if (!struct_def.fixed) {
        if (parser.file_identifier_.length()) {
          // Return the identifier
          code += Indent + "public static function " + struct_def.name;
          code += "Identifier()\n";
          code += Indent + "{\n";
          code += Indent + Indent + "return \"";
          code += parser.file_identifier_ + "\";\n";
          code += Indent + "}\n\n";

          // Check if a buffer has the identifier.
          code += Indent + "public static function " + struct_def.name;
          code += "BufferHasIdentifier(ByteBuffer $buf)\n";
          code += Indent + "{\n";
          code += Indent + Indent + "return self::";
          code += "__has_identifier($buf, self::";
          code += struct_def.name + "Identifier());\n";
          code += Indent + "}\n\n";
        }

        if (parser.file_extension_.length()) {
          // Return the extension
          code += Indent + "public static function " + struct_def.name;
          code += "Extension()\n";
          code += Indent + "{\n";
          code += Indent + Indent + "return \"" + parser.file_extension_;
          code += "\";\n";
          code += Indent + "}\n\n";
        }
      }

      // Generate the Init method that sets the field in a pre-existing
      // accessor object. This is to allow object reuse.
      InitializeExisting(struct_def, code_ptr);
      for (auto it = struct_def.fields.vec.begin();
      it != struct_def.fields.vec.end();
        ++it) {
        auto &field = **it;
        if (field.deprecated) continue;

        GenStructAccessor(struct_def, field, code_ptr);
      }

      if (struct_def.fixed) {
        // create a struct constructor function
        GenStructBuilder(struct_def, code_ptr);
      } else {
        // Create a set of functions that allow table construction.
        GenTableBuilders(parser, struct_def, code_ptr);
      }
      EndClass(code_ptr);
    }

    // Generate enum declarations.
    static void GenEnum(const EnumDef &enum_def, std::string *code_ptr) {
      if (enum_def.generated) return;

      GenComment(enum_def.doc_comment, code_ptr, nullptr);
      BeginEnum(enum_def.name, code_ptr);
      for (auto it = enum_def.vals.vec.begin();
      it != enum_def.vals.vec.end();
        ++it) {
        auto &ev = **it;
        GenComment(ev.doc_comment, code_ptr, nullptr);
        EnumMember(ev, code_ptr);
      }

      std::string &code = *code_ptr;
      code += "\n";
      code += Indent + "private static $names = array(\n";
      for (auto it = enum_def.vals.vec.begin();
        it != enum_def.vals.vec.end(); ++it) {
        auto &ev = **it;
        code += Indent + Indent + "\"" + ev.name + "\",\n";
      }

      code += Indent + ");\n\n";
      code += Indent + "public static function Name($e) {\n";
      code += Indent + Indent + "if (!isset(self::$names[$e])) {\n";
      code += Indent + Indent + Indent + "throw new \\Exception();\n";
      code += Indent + Indent + "}\n";
      code += Indent + Indent + "return self::$names[$e];\n";
      code += Indent + "}\n\n";
      EndEnum(code_ptr);
    }

    // Returns the function name that is able to read a value of the given type.
    static std::string GenGetter(const Type &type) {
      switch (type.base_type) {
      case BASE_TYPE_STRING: return "__string";
      case BASE_TYPE_STRUCT: return "__struct";
      case BASE_TYPE_UNION: return "__union";
      case BASE_TYPE_VECTOR: return GenGetter(type.VectorType());
      default:
        return "Get";
      }
    }

    // Returns the method name for use with add/put calls.
    static std::string GenMethod(const FieldDef &field) {
      return IsScalar(field.value.type.base_type)
        ? MakeCamel(GenTypeBasic(field.value.type))
        : (IsStruct(field.value.type) ? "Struct" : "Offset");
    }


    // Save out the generated code for a Php Table type.
    static bool SaveType(const Parser &parser, const Definition &def,
      const std::string &classcode, const std::string &path,
      bool needs_imports) {
      if (!classcode.length()) return true;

      std::string namespace_name;
      std::string namespace_dir = path;

      auto &namespaces = parser.namespaces_.back()->components;
      for (auto it = namespaces.begin(); it != namespaces.end(); ++it) {
        if (namespace_name.length()) {
          namespace_name += "\\";
          namespace_dir += kPathSeparator;
        }
        namespace_name += *it;
        namespace_dir += *it;
        EnsureDirExists(namespace_dir.c_str());
      }

      std::string code = "";
      BeginFile(namespace_name, needs_imports, &code);
      code += classcode;

      std::string filename = namespace_dir + kPathSeparator + def.name + ".php";
      return SaveFile(filename.c_str(), code, false);
    }

    static std::string GenTypeBasic(const Type &type) {
      static const char *ctypename[] = {
#define FLATBUFFERS_TD(ENUM, IDLTYPE, CTYPE, JTYPE, GTYPE, NTYPE, PTYPE) \
    #NTYPE,
        FLATBUFFERS_GEN_TYPES(FLATBUFFERS_TD)
#undef FLATBUFFERS_TD
      };
      return ctypename[type.base_type];
    }

    static std::string GenDefaultValue(const Value &value) {
      if (value.type.enum_def) {
        if (auto val = value.type.enum_def->ReverseLookup(
          atoi(value.constant.c_str()), false)) {
          return WrapInNameSpace(*value.type.enum_def) + "::" + val->name;
        }
      }

      switch (value.type.base_type) {
      case BASE_TYPE_BOOL:
        return value.constant == "0" ? "false" : "true";

      case BASE_TYPE_STRING:
        return "null";

      case BASE_TYPE_LONG:
      case BASE_TYPE_ULONG:
        if (value.constant != "0") {
          int64_t constant = StringToInt(value.constant.c_str());
          return NumToString(constant);
        }
        return "0";

      default:
        return value.constant;
      }
    }

    static std::string GenTypePointer(const Type &type) {
      switch (type.base_type) {
      case BASE_TYPE_STRING:
        return "string";
      case BASE_TYPE_VECTOR:
        return GenTypeGet(type.VectorType());
      case BASE_TYPE_STRUCT:
        return type.struct_def->name;
      case BASE_TYPE_UNION:
        // fall through
      default:
        return "Table";
      }
    }

    static std::string GenTypeGet(const Type &type) {
      return IsScalar(type.base_type)
        ? GenTypeBasic(type)
        : GenTypePointer(type);
    }

    // Create a struct with a builder and the struct's arguments.
    static void GenStructBuilder(const StructDef &struct_def,
      std::string *code_ptr) {
      std::string &code = *code_ptr;
      code += "\n";
      code += Indent + "/**\n";
      code += Indent + " * @return int offset\n";
      code += Indent + " */\n";
      code += Indent + "public static function Create" + struct_def.name;
      code += "(FlatBufferBuilder $builder";
      StructBuilderArgs(struct_def, "", code_ptr);
      code += "){\n";

      StructBuilderBody(struct_def, "", code_ptr);

      code += Indent + Indent + "return $builder->offset();\n";
      code += Indent + "}\n\n";
    }

}  // namespace php

  bool GeneratePhp(const Parser &parser,
    const std::string &path,
    const std::string & /*file_name*/,
    const GeneratorOptions & /*opts*/) {
    for (auto it = parser.enums_.vec.begin();
    it != parser.enums_.vec.end(); ++it) {
      std::string enumcode;
      php::GenEnum(**it, &enumcode);

      if (!php::SaveType(parser, **it, enumcode, path, false))
        return false;
    }

    for (auto it = parser.structs_.vec.begin();
    it != parser.structs_.vec.end(); ++it) {
      std::string declcode;
      php::GenStruct(parser, **it, &declcode);

      if (!php::SaveType(parser, **it, declcode, path, true))
        return false;
    }

    return true;
}
}  // namespace flatbuffers
