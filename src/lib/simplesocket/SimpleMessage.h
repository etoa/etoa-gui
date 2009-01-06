//----------------------------------------------------------------------------
//   Simple Message                                                 v1.1
//   Copyright (c) 2001          Wim Bokkers
//----------------------------------------------------------------------------
//
//   e.bokkers@hetnet.nl
//
//   bokkers@go.to
//
//----------------------------------------------------------------------------
//   This file is part of the Simple C++ Library
//
//   Permission is granted to use and distribute this library as long as the
//   copyright and this permission notice appear and provided that no changes
//   are made in any respect without contacting the author about the changes.
//
//   Any suggestions?
//   Please send an e-mail to the author with your name and e-mail address.
//
//   Revision history
//
//   v1.1 Feb. 2002
//   Memory leak in operator<< fixed. (Thanks to Florian Schaper)
//----------------------------------------------------------------------------

#ifndef SIMPLE_MESSAGE_H
#define SIMPLE_MESSAGE_H

#include <string>

#if defined(__sgi)
   #include <strstream.h>
#else
   #include <strstream>
   using namespace std;
#endif

//---
// The Simple C++ Library
// is placed in the namespace 'simple'
//---
namespace simple
{

//-----------------------------------------------------------------------------
// CLASS simple::Msg
// Used to store messages as if it is a input stream
// This makes it easier to create messages containing mixed types
// It has partly the same use as the std::strstream class.
// Example:
//    strstream aStream;
//    aStream << "This is a messsage ";
//    cout << aStream.str();
//
//    Msg aMsg;
//    aMsg << "This is a message";
//    cout << aMsg.str();
//
// Although it can be used partly the same way as strstream, Msg has
// the advantage that the << operator does not change the class type.
// (The Exception class makes use of this advantage)
// Example:
//    void write_strstream(strstream& astream)
//    {
//       cout << astream.str();
//    }
//
//    void write_Msg(Msg& amsg)
//    {
//       cout << amsg.str();
//    }
//
//    // The following is an error, since operator<< returns an ostream
//    // and NOT a strstream.
//    // The str() method is not available anymore
//    strstream aStream;
//    write_strstream( aStream << "This is a message" ); // Error !!
//
//    // The following code will work fine. The << operator returns
//    // a Msg object, having the str() method
//    Msg aMsg;
//    write_Msg(aMsg << "This is a message"); // Ok!
//-----------------------------------------------------------------------------
class Msg
{
   public:
      //---
      // Default constructor.
      // Makes the following constructs possible:
      //    Msg() << ....
      //---
      Msg()
        : messageM("")
      {
      }

      //---
      // Conversion constructor: convert string to Msg
      // This makes it possible to pass a string when a Msg object is
      // expected.
      // For example (Exception constructor expects a Msg):
      //   string err = "Error";
      //   throw Exception(err);
      //---
      Msg(const std::string& aStr)
      {
         messageM = aStr;
      }

      //---
      // Conversion constructor: convert char* to Msg
      // This makes it possible to pass a char* when a Msg object
      // is expected.
      // For example (Exception constructor expects a Msg):
      //   throw Exception("Error");
      //---
      Msg(const char* aStr)
      {
         messageM = aStr;
      }

      //---
      // Operator<<
      // Any type which can be streamed to an ostream can also be
      // streamed into a Msg object
      // Example:
      //   Msg() << "Length = "<< 1.8 "<< " m  Age = " << 31;
      //---
      template<class T>
      Msg& operator<<(T msgpart)
      {
         strstream msgStream;
         msgStream << msgpart;

         messageM = messageM + msgStream.str();

         // By default, strstream does not delete the character array.
         // Therefor, it should be deleted explicitly to avoid memory leaks.
         delete [] msgStream.str();

         return (*this);
      }

      //---
      // str method
      // Returns the message as a string
      //---
      std::string str() const
      {
         return messageM;
      }

   private:
      std::string messageM;
};


} // end of namespace simple



#endif